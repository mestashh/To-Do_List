#!/bin/bash

echo "Setting up Laravel project..."

mkdir -p storage/framework/cache
mkdir -p storage/framework/sessions
mkdir -p storage/framework/views
mkdir -p bootstrap/cache

chmod -R 775 storage bootstrap/cache

if [ ! -f .env ]; then
    cp .env.example .env
fi

composer install

php artisan key:generate
php artisan config:clear
php artisan view:clear
php artisan route:clear
