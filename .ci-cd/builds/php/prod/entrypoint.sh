#!/usr/bin/env bash

while ! nc -z db 3306; do
    echo "Waiting for database"
    sleep 1
done

php artisan migrate --force
php artisan route:cache
php artisan config:cache
php artisan doc:generate

php-fpm
