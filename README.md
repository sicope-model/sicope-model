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
cat var/selenoid/browsers.json | jq ".[].versions[].image" | xargs -L1 docker pull
docker pull selenoid/video-recorder:latest-release
```

## Development

```shell
yarn build
docker-compose up
symfony serve
symfony console doctrine:migrations:migrate
symfony console messenger:consume async
symfony console user:create
```

Navigate to http://localhost:8000 to create first model.

Tools:
* [Adminer](http://localhost:8888)
* [Mailhog](http://localhost:8025)
* [Selenoid UI](http://localhost:8080)

## Production

```shell
cp docker/.env.dist docker/.env
docker-compose -f docker-compose.yaml -f docker-compose.prod.yaml --env-file ./docker/.env up
docker-compose -f docker-compose.yaml -f docker-compose.prod.yaml --env-file ./docker/.env run admin bin/console user:create
```

For more information, see this [doc](https://github.com/dunglas/symfony-docker/blob/master/docs/production.md)

## Screenshots

![login](http://sicope-model.github.io/img/screenshots/login.png)

![dashboard](http://sicope-model.github.io/img/screenshots/dashboard.png)


## Links

* [Tutorial](https://sicope-model.github.io/docs/tutorial)
* [Documentations](https://sicope-model.github.io/docs)
* [News](http://sicope-model.github.io/blog)
