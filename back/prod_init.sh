#!/bin/bash

composer install --optimize-autoloader --no-dev

php artisan config:cache
php artisan route:cache
php artisan view:cache

php artisan migrate --force


# file permission
sudo chmod -R 755 bootstrap/cache
sudo chmod 777 storage/logs
