# SICOPE Model

Model Based Testing tool using Single Color Petrinet Model.

## Testing

### Requirements

* [Git](https://git-scm.com/downloads)
* [Docker 20.10+](https://docs.docker.com/get-docker/)

### Getting code

```shell
git clone https://github.com/sicope-model/sicope-model.git
cd sicope-model
```

### Download optional docker images

```shell
docker pull selenoid/video-recorder:latest-release
docker pull IMAGE_NAME:TAG # in var/selenoid/browsers.json
```

### Start

```shell
docker-compose --env-file docker/.env up
docker-compose exec admin php bin/console doctrine:schema:update --force
docker-compose exec admin php bin/console doctrine:migrations:migrate --no-interaction
docker-compose exec admin php bin/console app:add-user --admin
```

### Usage

Visit [Admin](https://localhost) to create first model.

## Contributing

### Requirements

* [Composer](https://getcomposer.org/download/)
* [Yarn](https://classic.yarnpkg.com/lang/en/docs/install/)

### Build code

```shell
composer install
yarn install
yarn build
```

### Debug

```diff
# docker-compose.yml
-    APP_ENV: prod
+    APP_ENV: dev

-    #volumes:
-    #  - .:/srv/app
+    volumes:
+      - .:/srv/app

-    #volumes:
-    #  - ./public:/srv/app/public
+    volumes:
+      - ./public:/srv/app/public
```

```diff
# .vscode/launch.json
        {
            "name": "Listen for Xdebug",
            "type": "php",
            "request": "launch",
+            "pathMappings": {
+                "/srv/app": "${workspaceRoot}",
+            },
            "port": 9003
        },
```

## Deployment

### Requirements

* [Nomad](https://www.nomadproject.io/downloads)

### Start

```shell
vault server -dev
nomad agent -config=client.hcl -bind=0.0.0.0 -dev
vault kv put -mount=secret sicope-model \
    postgres_user=user \
    postgres_password=password \
    postgres_db=db \
    status_uri=http://127.0.0.1:4444 \
    webdriver_uri=http://127.0.0.1:4444 \
    mailer_dsn=smtp://127.0.0.1:1025 \
    app_secret=a0b30e5d7a5a1f710b766e8ac601af11
nomad run prod.hcl
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
