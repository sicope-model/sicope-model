# SICOPE Model

Model Based Testing tool using Single Color Petrinet Model.

## Requirements

* [Git](https://git-scm.com/downloads)
* [Docker](https://docs.docker.com/get-docker/)
* [Docker Compose](https://docs.docker.com/compose/install/)

## Development

```shell
git clone https://github.com/sicope-model/sicope-model.git
cd sicope-model
docker-compose up
docker-compose exec worker php bin/console app:add-user
```

Visit [Admin](http://localhost) to create first model.

## Production

```shell
SERVER_NAME=your-domain-name.example.com \
APP_SECRET=ChangeMe \
POSTGRES_USER=user \
POSTGRES_PASSWORD=pass \
POSTGRES_DB=db \
MAILER_URL=gmail://e11codeduser%40gmail.com:e11codedpass@localhost \
docker-compose -f docker-compose.yml -f docker-compose.prod.yml up -d
```

For more information, see [Deploying in Production](https://github.com/dunglas/symfony-docker/blob/main/docs/production.md)

## Screenshots

![login](http://sicope-model.github.io/img/screenshots/login.png)

![dashboard](http://sicope-model.github.io/img/screenshots/dashboard.png)


## Links

* [Tutorial](https://sicope-model.github.io/docs/tutorial)
* [Documentations](https://sicope-model.github.io/docs)
* [News](http://sicope-model.github.io/blog)
