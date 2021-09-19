# SICOPE Model

Model Based Testing tool using Single Color Petrinet Model.

## Requirements

* [Git](https://git-scm.com/downloads)
* [Composer](https://getcomposer.org/)
* [Symfony CLI](https://symfony.com/download)
* [Docker](https://docs.docker.com/get-docker/)
* [Docker Compose](https://docs.docker.com/compose/install/)
* [Yarn](https://classic.yarnpkg.com/en/docs/install/#debian-stable)

## Installation

```shell
git clone https://github.com/sicope-model/sicope-model.git
cd sicope-model
docker-compose up


#composer install
#yarn install
#yarn build
#symfony serve
#symfony console doctrine:migrations:migrate
#symfony console messenger:consume async
#symfony console user:create
```

## Usage

Visit [Admin](http://localhost) to create first model.

Tools:
* [Adminer](http://localhost:8888)
* [Mailhog](http://localhost:8025)

## Production

```shell
cp docker/.env.dist docker/.env
docker-compose -f docker-compose.prod.yaml --env-file ./docker/.env up
docker-compose -f docker-compose.prod.yaml --env-file ./docker/.env run admin bin/console user:create
```

For more information, see this [doc](https://github.com/dunglas/symfony-docker/blob/master/docs/production.md)

## Screenshots

![login](http://sicope-model.github.io/img/screenshots/login.png)

![dashboard](http://sicope-model.github.io/img/screenshots/dashboard.png)


## Links

* [Tutorial](https://sicope-model.github.io/docs/tutorial)
* [Documentations](https://sicope-model.github.io/docs)
* [News](http://sicope-model.github.io/blog)
