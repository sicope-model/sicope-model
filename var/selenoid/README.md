# Selenoid

An example Selenoid instance on your local.

## Requirements

* [Jq](https://stedolan.github.io/jq/download/)
* [Docker](https://docs.docker.com/get-docker/)
* [Docker Compose](https://docs.docker.com/compose/install/)

## Installation

Update `var/selenoid/browsers.json` to add or remove browsers as you like, then run these commands:

```shell
CD var/selenoid
cat browsers.json | jq ".[].versions[].image" | xargs -L1 docker pull
docker pull selenoid/video-recorder:latest-release
docker-compose up
```

## Usage

Visit [Selenoid UI](http://localhost:8080) to manage browser sessions.
