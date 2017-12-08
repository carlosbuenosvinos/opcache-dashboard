ARG OPCACHE_DASHBOARD_PHP_VERSION=7.2

FROM php:$OPCACHE_DASHBOARD_PHP_VERSION-apache

COPY opcache.php /var/www/html/opcache.php

RUN docker-php-ext-install opcache
