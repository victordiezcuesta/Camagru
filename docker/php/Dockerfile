FROM php:8.3-fpm

RUN apt-get update \
    && apt-get install -y \
        libpng-dev \
        libjpeg62-turbo-dev \
    && docker-php-ext-configure gd --with-jpeg \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        gd \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html