# the different stages of this Dockerfile are meant to be built into separate images
# https://docs.docker.com/develop/develop-images/multistage-build/#stop-at-a-specific-build-stage
# https://docs.docker.com/compose/compose-file/#target


# https://docs.docker.com/engine/reference/builder/#understand-how-arg-and-from-interact
ARG PHP_VERSION=8.1
ARG CADDY_VERSION=2
ARG NODE_VERSION=16

FROM php:${PHP_VERSION}-cli-alpine AS build-worker

RUN curl -sSLf \
        -o /usr/local/bin/install-php-extensions \
        https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions && \
    chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions zip pdo_pgsql redis && \
	rm /usr/local/bin/install-php-extensions

WORKDIR /srv/app

COPY . .

RUN set -eux; \
	mkdir -p var/cache var/log; \
	curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer; \
	composer install --prefer-dist --no-dev --no-progress --no-scripts --no-interaction; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer symfony:dump-env prod; \
    chmod +x bin/console; sync; \
    bin/console cache:clear; \
	composer clear-cache; \
	rm /usr/local/bin/composer;

COPY docker/prod/conf.d/symfony.ini $PHP_INI_DIR/conf.d/symfony.ini

CMD ["php", "/srv/app/bin/console", "messenger:consume", "async"]

FROM php:${PHP_VERSION}-cli-alpine AS build-public

RUN apk add --no-cache \
    nodejs \
    yarn

WORKDIR /srv/app

COPY --from=build-worker /srv/app /srv/app
RUN bin/console assets:install

RUN yarn install; \
	yarn run encore production;

FROM php:${PHP_VERSION}-fpm-alpine AS build-admin

RUN curl -sSLf \
        -o /usr/local/bin/install-php-extensions \
        https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions && \
    chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions \
        # php webdriver
        zip \
        # format date/time for EasyAdmin
        intl \
        pdo_pgsql \
        redis \
        opcache && \
	rm /usr/local/bin/install-php-extensions

RUN  apk add --no-cache graphviz

RUN ln -s $PHP_INI_DIR/php.ini-production $PHP_INI_DIR/php.ini
COPY docker/prod/conf.d/symfony.ini $PHP_INI_DIR/conf.d/symfony.ini
COPY docker/prod/php-fpm.d/z-symfony.conf /usr/local/etc/php-fpm.d/z-symfony.conf

WORKDIR /srv/app

COPY --from=build-worker /srv/app /srv/app
COPY --from=build-public /srv/app/public/build/manifest.json public/build/manifest.json
COPY --from=build-public /srv/app/public/build/entrypoints.json public/build/entrypoints.json

CMD ["php-fpm"]

FROM caddy:${CADDY_VERSION} AS build-caddy

WORKDIR /srv/app

COPY --from=build-public /srv/app/public public/
COPY docker/prod/Caddyfile /etc/caddy/Caddyfile

FROM build-worker as build-worker-debug

RUN curl -sSLf \
        -o /usr/local/bin/install-php-extensions \
        https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions && \
    chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions xdebug && \
	rm /usr/local/bin/install-php-extensions

COPY docker/dev/conf.d/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY docker/dev/conf.d/error_reporting.ini /usr/local/etc/php/conf.d/error_reporting.ini

FROM build-admin as build-admin-debug

RUN curl -sSLf \
        -o /usr/local/bin/install-php-extensions \
        https://github.com/mlocati/docker-php-extension-installer/releases/latest/download/install-php-extensions && \
    chmod +x /usr/local/bin/install-php-extensions && \
    install-php-extensions xdebug && \
	rm /usr/local/bin/install-php-extensions

COPY docker/dev/conf.d/xdebug.ini /usr/local/etc/php/conf.d/docker-php-ext-xdebug.ini
COPY docker/dev/conf.d/error_reporting.ini /usr/local/etc/php/conf.d/error_reporting.ini
