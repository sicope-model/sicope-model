# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
ARG PHP_VERSION=8.0
ARG CADDY_VERSION=2
ARG NODE_VERSION=16

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

WORKDIR /srv/app

COPY . .

RUN set -eux; \
	mkdir -p var/cache var/log; \
	curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer; \
	composer install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer symfony:dump-env prod; \
	composer run-script --no-dev post-install-cmd; \
    chmod +x bin/console; sync; \
	composer clear-cache; \
	rm /usr/local/bin/composer;
VOLUME /srv/app/var

COPY docker/php/conf.d/symfony.prod.ini $PHP_INI_DIR/conf.d/symfony.ini

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]

CMD ["php", "/srv/app/bin/console", "messenger:consume", "async"]

FROM node:${NODE_VERSION}-alpine as build_public

WORKDIR /srv/app

COPY package.json webpack.config.js yarn.lock /srv/app/
COPY assets /srv/app/assets
COPY --from=build_worker /srv/app/vendor/symfony/ux-chartjs/Resources/assets /srv/app/vendor/symfony/ux-chartjs/Resources/assets

RUN apk add --no-cache --virtual .build-deps python3 make g++; \
	yarn install; \
	yarn run encore production; \
	apk del .build-deps

FROM php:${PHP_VERSION}-fpm-alpine AS build_admin

# persistent / runtime deps
RUN apk add --no-cache \
		acl \
		gnu-libiconv \
		graphviz \
	;

# install gnu-libiconv and set LD_PRELOAD env to make iconv work fully on Alpine image.
# see https://github.com/docker-library/php/issues/240#issuecomment-763112749
ENV LD_PRELOAD /usr/lib/preloadable_libiconv.so

ARG APCU_VERSION=5.1.20
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

COPY docker/php/docker-healthcheck.sh /usr/local/bin/docker-healthcheck
RUN chmod +x /usr/local/bin/docker-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["docker-healthcheck"]

RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY docker/php/conf.d/symfony.prod.ini $PHP_INI_DIR/conf.d/symfony.ini
COPY docker/php/pool.d/symfony.conf /usr/local/etc/php-fpm.d/z-symfony.conf
COPY docker/php/php-fpm.d/zz-docker.conf /usr/local/etc/php-fpm.d/zz-docker.conf

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

VOLUME /var/run/php

WORKDIR /srv/app

COPY --from=build_worker /srv/app /srv/app
COPY --from=build_public /srv/app/public /srv/app/public
VOLUME /srv/app/var

ENTRYPOINT ["docker-entrypoint"]
CMD ["php-fpm"]

FROM caddy:${CADDY_VERSION} AS build_caddy

WORKDIR /srv/app

COPY --from=build_admin /srv/app/public public/
COPY docker/caddy/Caddyfile /etc/caddy/Caddyfile
