# SICOPE Model

Model Based Testing tool using Single Color Petrinet Model.

## Requirements

* [Git](https://git-scm.com/downloads)
* [Docker](https://docs.docker.com/get-docker/)
* [Docker Compose](https://docs.docker.com/compose/install/)

## Getting code

```shell
git clone https://github.com/sicope-model/sicope-model.git
cd sicope-model
```

## Testing

```shell
docker-compose --env-file docker/.env -f docker-compose.yml -f docker-compose.dependencies.yml -f docker-compose.testing.yml up
docker-compose exec admin php bin/console doctrine:schema:update --force
docker-compose exec admin php bin/console doctrine:migrations:migrate --no-interaction
docker-compose exec admin php bin/console app:add-user --admin
```

Visit [Admin](http://localhost) to create first model.

## Contributing

```shell
docker-compose --env-file docker/.env -f docker-compose.dependencies.yml up
docker pull selenoid/video-recorder:latest-release
docker pull IMAGE_NAME:TAG # in var/selenoid/browsers.json
sudo apt install php php-fpm php-pgsql php-intl php-zip graphviz
composer install
yarn install
yarn build
symfony serve --port=8000
symfony console messenger:consume async
```

Visit [Admin](http://localhost:8000) to test new code.

## Production

Set values for these environment variables:

```yaml
#docker/.env
SERVER_NAME=your-domain-name.example.com
APP_SECRET=ChangeMe
POSTGRES_USER=user
POSTGRES_PASSWORD=pass
POSTGRES_DB=db
POSTGRES_HOST=db.example.com
POSTGRES_PORT=5432
MAILER_URL=gmail://e11codeduser%40gmail.com:e11codedpass@localhost
```

Then run:

```shell
docker-compose --env-file docker/.env -f docker-compose.yml -f docker-compose.prod.yml up -d
docker-compose exec admin php bin/console doctrine:schema:update --force
docker-compose exec admin php bin/console doctrine:migrations:migrate --no-interaction
docker-compose exec admin php bin/console app:add-user --admin
```

For more information, see [Deploying in Production](https://github.com/dunglas/symfony-docker/blob/main/docs/production.md)

## Screenshots

![login](http://sicope-model.github.io/img/screenshots/login.png)

![dashboard](http://sicope-model.github.io/img/screenshots/dashboard.png)


## Links

* [Tutorial](https://sicope-model.github.io/docs/tutorial)
* [Documentations](https://sicope-model.github.io/docs)
* [News](http://sicope-model.github.io/blog)
