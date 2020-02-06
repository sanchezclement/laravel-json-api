#!/usr/bin/env bash
set -e

while ! nc -z db 3306; do
    echo "Waiting for database"
    sleep 1
done

php artisan migrate --force
php artisan db:seed
php artisan passport:install
php artisan passport:client --personal

vendor/bin/phpunit --coverage-text --colors=never
# Disabled because it causes memory leaks
#vendor/bin/phpqa --report
