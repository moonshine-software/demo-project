FROM php:8.4-fpm AS php

WORKDIR /var/www/app

ARG user
ARG uid

RUN apt-get update && apt-get install -y \
    curl \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libc6 \
    zip \
    unzip \
    default-mysql-client

RUN apt-get clean && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip

RUN pecl install redis

COPY --from=composer:2.5.8 /usr/bin/composer /usr/bin/composer

RUN useradd -G www-data,root -u $uid -d /home/$user $user

RUN mkdir -p /home/$user/.composer && \
    chown -R $user:$user /home/$user

COPY ./docker/dockerfiles/php/config/php-dev.ini /usr/local/etc/php/conf.d/php.ini
COPY ./docker/dockerfiles/php/config/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

FROM php AS dev
COPY . /var/www/app
RUN if [ -d '/var/www/app/storage' ]; then chmod -R 775 ./storage ./bootstrap/cache; fi
USER $user
