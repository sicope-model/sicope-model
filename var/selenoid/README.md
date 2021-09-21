# Selenoid

An example Selenoid instance on your local.

## Requirements

* [Jq](https://stedolan.github.io/jq/download/)

## Installation

Update `var/selenoid/browsers.json` to add or remove browsers as you like, then run these commands:

```shell
cd var/selenoid
cat browsers.json | jq ".[].versions[].image" | xargs -L1 docker pull
docker pull selenoid/video-recorder:latest-release
```
