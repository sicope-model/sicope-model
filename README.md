SICOPE Model
=========
Model Based Testing tool using Single Color Petrinet Model.

Usage
--------------------

1. Download
```shell
git clone https://github.com/sicope-model/sicope-model.git
cd sicope-model
```

2. Install
```shell
composer install
cp docker/.env.dist docker/.env
bin/console app:dump-browsers

# Pull docker images (some are BIG)
cat config/selenoid/browsers.json | jq ".[].versions[].image" | xargs -L1 docker pull
docker pull selenoid/video-recorder:latest-release
```

3. For Development
```shell
docker-compose --env-file ./docker/.env up
```

4. For [Production](https://github.com/dunglas/symfony-docker/blob/master/docs/production.md)
```
docker-compose pull
SERVER_NAME=your-domain-name.example.com docker-compose -f docker-compose.yaml -f docker-compose.prod.yaml --env-file ./docker/.env up
```

5. Create User
```
docker-compose --env-file ./docker/.env exec admin bin/console user:create
```

Screenshots
--------------------

![page-login](https://user-images.githubusercontent.com/8649070/42580602-9e3bd2b0-8533-11e8-9a37-4ebb02765559.jpg)

![page-admin](https://user-images.githubusercontent.com/8649070/42580601-9e100496-8533-11e8-93bf-9d74e721ccd5.png)


Links
--------------------

* [Tutorial](https://sicope-model.github.io/docs/tutorial)
* [Documentations](https://sicope-model.github.io/docs)
* [News](https://mbtbundle.org/blog)
