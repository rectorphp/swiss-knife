#!/usr/bin/env bash
set -euo pipefail

cd "$(dirname "$0")/.."
source docker/common.sh

install_dependencies
docker compose run --rm php vendor/bin/phpunit
