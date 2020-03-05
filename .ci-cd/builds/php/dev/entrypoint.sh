#!/usr/bin/env bash

if [[ ! -d vendor ]]; then
    echo "Install composer dependencies"
    composer install
fi

php-fpm
