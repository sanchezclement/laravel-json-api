##############
# BASE IMAGE #
##############
FROM php:7.3.11-fpm-alpine

# Create a writable directory in the root of the container
RUN mkdir /.config && chmod 777 /.config
# Define environment to set up composer home
ENV COMPOSER_HOME /.config/.composer

# Update & install dependency packages
RUN apk update && apk add bash git libpng-dev

# Install composer
COPY --from=composer:1.9.1 /usr/bin/composer /usr/bin/composer

# Install mysql PHP extension
RUN docker-php-ext-install pdo_mysql gd

# Change the working directory
WORKDIR /code
# Copy the dependency file
COPY composer.json composer.lock ./
# Install the composer dependencies
RUN composer install --no-progress --no-scripts --prefer-dist --no-autoloader --no-dev
# Finally copy all the app
COPY . .
# Dump the autoload
RUN composer dump-autoload --no-scripts --no-dev --optimize

# The production environment entrypoint
ENTRYPOINT bash .ci-cd/builds/php/prod/entrypoint.sh

