FROM php:8.1-apache
# https://hub.docker.com/_/php
RUN curl -sL https://deb.nodesource.com/setup_16.x | bash -

RUN apt-get update \
    && apt-get install -y git zip unzip nodejs vim libpng-dev libpq-dev libfreetype6-dev libjpeg62-turbo-dev libzip-dev libgmp-dev \
    && docker-php-ext-install pdo_mysql pdo_pgsql
RUN docker-php-ext-install gd

COPY php-base.ini $PHP_INI_DIR/conf.d/

COPY httpd-base.conf /etc/apache2/sites-available/laravel.conf
RUN a2dissite 000-default.conf && a2ensite laravel.conf && a2enmod rewrite

RUN curl -sS https://getcomposer.org/installer | php && mv composer.phar /usr/local/bin/composer
