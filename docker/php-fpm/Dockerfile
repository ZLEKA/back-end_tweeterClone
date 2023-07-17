FROM php:8.2-fpm-alpine
RUN apk update \
    && apk add autoconf git libmcrypt-dev libzip-dev zip bash gcc \
    && docker-php-ext-install pcntl mysqli pdo pdo_mysql zip opcache \
    && docker-php-ext-enable pdo_mysql 