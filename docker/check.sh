#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."
source docker/common.sh

install_dependencies

docker compose run --rm php composer validate --ansi
docker compose run --rm php vendor/bin/phpunit
docker compose run --rm php vendor/bin/phpstan analyse --memory-limit=512M --ansi
docker compose run --rm php vendor/bin/ecs check --ansi
docker compose run --rm php vendor/bin/composer-dependency-analyser
