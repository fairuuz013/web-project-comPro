FROM php:8.2-apache

# Aktifkan pdo & mysqli
RUN docker-php-ext-install pdo pdo_mysql mysqli

# Aktifkan mod_rewrite Apache
RUN a2enmod rewrite
