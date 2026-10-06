FROM php:8.5-fpm AS php-base

WORKDIR /app

RUN apt-get update \
    && apt-get install --no-install-recommends --yes \
        libfcgi-bin \
        unzip \
    && docker-php-ext-install pdo_mysql \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer
COPY docker/php/php.ini /usr/local/etc/php/conf.d/app.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/zz-app.conf

FROM php-base AS dev

ENV COMPOSER_HOME=/tmp/composer

ARG HOST_UID=1000
ARG HOST_GID=1000

RUN groupmod --non-unique --gid "${HOST_GID}" www-data \
    && usermod --non-unique --uid "${HOST_UID}" --gid "${HOST_GID}" www-data \
    && mkdir -p /tmp/composer /app/var/cache/smarty/templates_c \
    && chown -R www-data:www-data /tmp/composer /app

USER www-data

EXPOSE 9000

HEALTHCHECK --interval=10s --timeout=3s --start-period=10s --retries=5 \
    CMD SCRIPT_NAME=/fpm-ping SCRIPT_FILENAME=/fpm-ping REQUEST_METHOD=GET \
        cgi-fcgi -bind -connect 127.0.0.1:9000 | grep --quiet pong || exit 1

CMD ["php-fpm"]
