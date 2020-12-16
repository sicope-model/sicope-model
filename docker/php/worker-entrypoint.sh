#!/bin/sh
set -e

echo "Waiting for db to be ready..."
ATTEMPTS_LEFT_TO_REACH_DATABASE=60
until [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ] || bin/console doctrine:query:sql "SELECT 1" > /dev/null 2>&1; do
  sleep 1
  ATTEMPTS_LEFT_TO_REACH_DATABASE=$((ATTEMPTS_LEFT_TO_REACH_DATABASE-1))
  echo "Still waiting for db to be ready... Or maybe the db is not reachable. $ATTEMPTS_LEFT_TO_REACH_DATABASE attempts left"
done

if [ $ATTEMPTS_LEFT_TO_REACH_DATABASE -eq 0 ]; then
  echo "The db is not up or not reachable"
  exit 1
else
   echo "The db is now ready and reachable"
fi

if ls -A migrations/*.php > /dev/null 2>&1; then
  bin/console doctrine:migrations:migrate --no-interaction
fi

exec docker-php-entrypoint "$@"
