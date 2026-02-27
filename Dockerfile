FROM php:8.2-fpm

RUN apt-get update && apt-get install -y \
    zip unzip curl git \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    libcurl4-openssl-dev \
    libjpeg-dev \
    libfreetype6-dev \
    && docker-php-ext-install \
    pdo_mysql \
    mbstring \
    intl \
    zip \
    exif \
    gd

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
