ARG PHP_VERSION=8.0
ARG CADDY_VERSION=2

FROM composer:latest AS vendor

WORKDIR /srv/app

COPY composer.json composer.lock /srv/app/

RUN composer install \
    --ignore-platform-reqs \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --no-dev \
    --no-progress

FROM node:15-alpine as public

WORKDIR /srv/app

COPY package.json postcss.config.js webpack.config.js yarn.lock /srv/app/
COPY assets /srv/app/assets

RUN apk add --no-cache --virtual .gyp python make g++; \
    yarn install; \
    yarn run encore production; \
    apk del .gyp

FROM composer:latest AS nopublic

WORKDIR /srv/app

COPY . .
COPY --from=vendor /srv/app/vendor /srv/app/vendor

# https://github.com/dunglas/symfony-docker/blob/master/Dockerfile#L90
RUN set -eux; \
	mkdir -p var/cache var/log; \
	composer dump-autoload --classmap-authoritative --no-dev; \
	composer symfony:dump-env prod; \
	composer run-script --no-dev post-install-cmd; sync

FROM php:${PHP_VERSION}-fpm-alpine AS admin

RUN apk --no-cache add postgresql-dev libpq acl icu-libs graphviz; \
    docker-php-ext-install pdo_pgsql intl; \
    apk del postgresql-dev

COPY docker/php/conf.d/symfony.prod.ini $PHP_INI_DIR/conf.d/symfony.ini
COPY docker/fpm/pool.d/symfony.conf /usr/local/etc/php-fpm.d/z-symfony.conf

COPY --from=nopublic /srv/app /srv/app
COPY --from=public /srv/app/public /srv/app/public
VOLUME /srv/app/var

COPY docker/php/admin-healthcheck.sh /usr/local/bin/admin-healthcheck
RUN chmod +x /usr/local/bin/admin-healthcheck

HEALTHCHECK --interval=10s --timeout=3s --retries=3 CMD ["admin-healthcheck"]

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]

WORKDIR /srv/app
CMD ["php-fpm"]

FROM php:${PHP_VERSION}-cli-alpine AS worker

RUN apk --no-cache add postgresql-dev libpq acl libxslt-dev libxslt; \
    docker-php-ext-install pdo_pgsql xsl; \
    apk del postgresql-dev libxslt-dev

COPY --from=nopublic /srv/app /srv/app
VOLUME /srv/app/var

COPY docker/php/conf.d/symfony.prod.ini $PHP_INI_DIR/conf.d/symfony.ini

COPY docker/php/docker-entrypoint.sh /usr/local/bin/docker-entrypoint
RUN chmod +x /usr/local/bin/docker-entrypoint

ENTRYPOINT ["docker-entrypoint"]

WORKDIR /srv/app
CMD ["php", "/srv/app/bin/console", "messenger:consume", "async"]

FROM caddy:${CADDY_VERSION} AS caddy

WORKDIR /srv/app

COPY --from=public /srv/app/public public/
COPY docker/caddy/Caddyfile /etc/caddy/Caddyfile
