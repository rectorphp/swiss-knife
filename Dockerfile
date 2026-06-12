FROM php:8.4-cli-bookworm

RUN apt-get update \
    && apt-get install -y --no-install-recommends curl git unzip libicu-dev $PHPIZE_DEPS \
    && docker-php-ext-install intl \
    && curl -fsSL https://github.com/krakjoe/pcov/archive/refs/tags/v1.0.12.tar.gz -o /tmp/pcov.tar.gz \
    && tar xzf /tmp/pcov.tar.gz -C /tmp \
    && cd /tmp/pcov-1.0.12 \
    && phpize \
    && ./configure \
    && make -j"$(nproc)" \
    && make install \
    && docker-php-ext-enable pcov \
    && rm -rf /tmp/pcov.tar.gz /tmp/pcov-1.0.12 \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app
