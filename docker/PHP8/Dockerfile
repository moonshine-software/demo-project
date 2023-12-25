FROM php:8.3-fpm-alpine3.18

RUN apk add --no-cache --update \
  bzip2-dev \
  enchant2-dev \
  gmp-dev \
  imap-dev \
  icu-dev \
  openldap-dev \
  freetds-dev \
  libxml2-dev \
  tidyhtml-dev  \
  libxslt-dev \
  libzip-dev \
  musl-dev \
  jpeg-dev \
  libpng-dev \
  oniguruma-dev \
  freetype  \
  libpng  \
  libjpeg-turbo  \
  freetype-dev  \
  libjpeg-turbo-dev \
  bash \
  openrc
  #supervisor

RUN docker-php-ext-configure intl --enable-intl
RUN docker-php-ext-configure gd --with-freetype --with-jpeg

RUN docker-php-ext-install -j$(nproc) gd \
    pdo \
    mysqli  \
    pdo_mysql  \
    intl  \
    mbstring  \
    zip  \
    pcntl \
    exif  \
    opcache \
    soap \
    && docker-php-source delete

#Installing composer
RUN curl -sS https://getcomposer.org/installer -o composer-setup.php
RUN php composer-setup.php --install-dir=/usr/local/bin --filename=composer
RUN rm -rf composer-setup.php

#Installing redis
RUN apk add --no-cache pcre-dev $PHPIZE_DEPS \
        && pecl install redis \
        && docker-php-ext-enable redis.so

##Installing xdebug
#RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS
#RUN apk add --update linux-headers
#RUN pecl install xdebug-3.3.1
#RUN docker-php-ext-enable xdebug
#RUN apk del -f .build-deps

RUN rm -rf /tmp/* /var/cache/apk/*

WORKDIR /var/www/moonshine-demo

ARG docker_user
ENV user=${docker_user}

RUN adduser -D -u 1000 ${user}
RUN addgroup ${user} www-data

RUN chown -R www-data:www-data /var/www/

##For supervisor
#RUN mkdir -p /etc/supervisor/conf.d \
# && mkdir -p /var/log/supervisor

RUN chmod 777 -R /tmp && chmod o+t -R /tmp
RUN chmod 777 -R /var/log && chmod o+t -R /var/log
RUN chmod 777 -R /var/run && chmod o+t -R /var/run

RUN mv /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

COPY ./conf.d /usr/local/etc/php/conf.d

##For supervisor
#COPY supervisor/supervisord.conf /etc/supervisor/supervisord.conf
#COPY entrypoint.sh /home/entrypoint.sh
#RUN chmod 755 /home/entrypoint.sh
#ENTRYPOINT ["/home/entrypoint.sh"]

USER ${user}