FROM php:8.0-fpm-bullseye

# libzip und git installieren, da diese Abhängigkeiten von composer sind
RUN apt update && apt install -y libzip-dev git

RUN pecl install xdebug \
    && docker-php-ext-enable xdebug \
    && docker-php-ext-install mysqli zip

COPY --from=composer:2.1 /usr/bin/composer /usr/local/bin/composer

RUN whoami
USER www-data
COPY --chown=www-data:www-data . /var/www/html
WORKDIR /var/www/html
RUN /usr/local/bin/composer install --no-progress
