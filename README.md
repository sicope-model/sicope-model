# SICOPE Model

Model Based Testing tool using Single Color Petrinet Model.

## Requirements

* [Composer](https://getcomposer.org/)
* [Symfony CLI](https://symfony.com/download)
* [Jq](https://stedolan.github.io/jq/download/)
* [Docker](https://docs.docker.com/get-docker/)
* [Docker Compose](https://docs.docker.com/compose/install/)
* [Yarn](https://classic.yarnpkg.com/en/docs/install/#debian-stable)

## Installation

```shell
git clone https://github.com/sicope-model/sicope-model.git
cd sicope-model

composer install
bin/console app:dump-browsers
# Pull docker images (some are BIG)
cat config/selenoid/browsers.json | jq ".[].versions[].image" | xargs -L1 docker pull
docker pull selenoid/video-recorder:latest-release
```

## Development

```shell
yarn build
docker-compose up
symfony serve
bin/console doctrine:migrations:migrate
bin/console messenger:consume async
bin/console user:create
```

## Production

```shell
cp docker/.env.dist docker/.env
docker-compose -f docker-compose.yaml -f docker-compose.prod.yaml --env-file ./docker/.env up
docker-compose -f docker-compose.yaml -f docker-compose.prod.yaml --env-file ./docker/.env run admin bin/console user:create
```

For more information, see this [doc](https://github.com/dunglas/symfony-docker/blob/master/docs/production.md)

## Screenshots

![page-login](https://user-images.githubusercontent.com/8649070/42580602-9e3bd2b0-8533-11e8-9a37-4ebb02765559.jpg)

![page-admin](https://user-images.githubusercontent.com/8649070/42580601-9e100496-8533-11e8-93bf-9d74e721ccd5.png)


## Links

* [Tutorial](https://sicope-model.github.io/docs/tutorial)
* [Documentations](https://sicope-model.github.io/docs)
* [News](https://mbtbundle.org/blog)
