SICOPE Model
=========
Model Based Testing tool using Single Color Petrinet Model.

Usage
--------------------

Download
```
git clone https://github.com/sicope-model/sicope-model.git
cd sicope-model
```

Development
```
composer install
docker-compose up
docker-compose exec admin bin/console user:create
```

[Production](https://github.com/dunglas/symfony-docker/blob/master/docs/production.md)
```
docker-compose pull
SERVER_NAME=your-domain-name.example.com docker-compose -f docker-compose.yaml -f docker-compose.prod.yaml up
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
