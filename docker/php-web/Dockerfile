FROM php:8.0-fpm-bullseye

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install mysqli

COPY --from=composer:2.1 /usr/bin/composer /usr/local/bin/composer

COPY . /var/www/
WORKDIR /var/www/
RUN /usr/local/bin/composer install
