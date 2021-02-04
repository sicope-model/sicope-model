# https://github.com/dunglas/symfony-docker/blob/main/Dockerfile
ARG PHP_VERSION=7.4
ARG CADDY_VERSION=2
ARG NODE_VERSION=15

FROM node:${NODE_VERSION}-alpine as build_public

WORKDIR /srv/app

COPY package.json postcss.config.js webpack.config.js yarn.lock /srv/app/
COPY assets /srv/app/assets

RUN apk add --no-cache --virtual .build-deps python make g++; \
    yarn install; \
    yarn run encore production; \
    apk del .build-deps

FROM php:${PHP_VERSION}-fpm-alpine AS build_admin

# persistent / runtime deps
RUN apk add --no-cache \
        acl \
        graphviz \
    ;

# PHP extensions
ARG APCU_VERSION=5.1.19
RUN set -eux; \
	apk add --no-cache --virtual .build-deps \
	    $PHPIZE_DEPS \
	    icu-dev \
	    libzip-dev \
	    zlib-dev \
	    postgresql-dev \
	    libxslt-dev \
	; \
	\
	docker-php-ext-configure zip; \
	docker-php-ext-install -j$(nproc) \
	    intl \
	    zip \
	    pdo_pgsql \
	    xsl \
	; \
	pecl install \
	    apcu-${APCU_VERSION} \
	; \
	pecl clear-cache; \
	docker-php-ext-enable \
	    apcu \
	    opcache \
	; \
	\
	runDeps="$( \
	    scanelf --needed --nobanner --format '%n#p' --recursive /usr/local/lib/php/extensions \
	        | tr ',' '\n' \
	        | sort -u \
	        | awk 'system("[ -e /usr/local/lib/" $1 " ]") == 0 { next } { print "so:" $1 }' \
	)"; \
	apk add --no-cache --virtual .phpexts-rundeps $runDeps; \
	\
	apk del .build-deps

RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY docker/php/conf.d/symfony.prod.ini $PHP_INI_DIR/conf.d/symfony.ini
COPY docker/php/pool.d/symfony.conf /usr/local/etc/php-fpm.d/z-symfony.conf

RUN set -eux; \
	{ \
		echo '[www]'; \
		echo 'ping.path = /ping'; \
	} | tee /usr/local/etc/php-fpm.d/docker-healthcheck.conf

ENV APP_ENV=prod
WORKDIR /srv/app

COPY . .

RUN set -eux; \
	mkdir -p var/cache var/log; \
	apk add --no-cache git; \
	curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer; \
	composer install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer symfony:dump-env prod; \
	composer run-script --no-dev post-install-cmd; sync; \
    composer clear-cache; \
    rm /usr/local/bin/composer; \
    apk del git
VOLUME /srv/app/var

COPY docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

COPY --from=build_public /srv/app/public /srv/app/public

FROM php:${PHP_VERSION}-cli-alpine AS build_worker

# persistent / runtime deps
RUN apk add --no-cache \
        acl \
        libpq \
        libxslt \
        libzip \
    ;

# PHP extensions
RUN apk add --no-cache --virtual .build-deps postgresql-dev libxslt-dev libzip-dev; \
    docker-php-ext-install pdo_pgsql xsl zip; \
    apk del .build-deps

ENV APP_ENV=prod
WORKDIR /srv/app

COPY --from=build_admin /srv/app /srv/app
VOLUME /srv/app/var

COPY docker/php/conf.d/symfony.prod.ini $PHP_INI_DIR/conf.d/symfony.ini

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]

CMD ["php", "/srv/app/bin/console", "messenger:consume", "async"]

FROM caddy:${CADDY_VERSION} AS build_caddy

WORKDIR /srv/app

COPY --from=build_admin /srv/app/public /srv/app/public
COPY docker/caddy/Caddyfile /etc/caddy/Caddyfile
