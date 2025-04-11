#!/usr/bin/env bash

# change directory
cd /var/www/contacts.virtulab.in || exit

# enter a maintenance mode
php artisan down

# get the latest code
git pull

# Autoloader Optimization - laravel docs
composer install --optimize-autoloader --no-dev

# migrate the database
php artisan migrate --force

# Clear existing configuration cache
php artisan config:cache
# Optimizing Route Loading - laravel docs
php artisan route:cache
# Optimizing View Loading - laravel docs
php artisan view:cache

# exit the maintenance mode
php artisan up

#reload horizon if using horizon
#php artisan horizon:terminate

#restart the main supervisor if something goes wrong
#sudo service supervisor restart
