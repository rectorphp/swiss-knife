#!/usr/bin/env bash

install_dependencies() {
    if [[ ! -f vendor/bin/phpunit ]]; then
        docker compose run --rm php composer update --no-interaction
        return
    fi

    docker compose run --rm php composer install --no-interaction \
        || docker compose run --rm php composer update --no-interaction
}
