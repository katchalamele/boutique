FROM php:7.4-apache

RUN apt-get update && apt-get install -y \
        zip \
        sqlite3 libsqlite3-dev

RUN docker-php-ext-install -j$(nproc) pdo pdo_mysql pdo_sqlite

# Install composer
COPY --from=composer/composer:latest-bin /composer /usr/bin/composer
RUN mkdir /var/composer
ENV COMPOSER_HOME /var/composer
ENV COMPOSER_MEMORY_LIMIT -1
ENV COMPOSER_ALLOW_SUPERUSER 1

WORKDIR /var/www/boutique