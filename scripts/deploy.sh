#!/usr/bin/env bash

# change directory
cd /var/www/contacts.virtulab.in || exit

# enter a maintenance mode
echo "🚧 Entering maintenance mode..."
php artisan down

# get the latest code
echo "📥 Pulling latest changes..."
git pull

# Autoloader Optimization - laravel docs
echo "📦 Installing composer dependencies..."
composer install --optimize-autoloader --no-dev

echo "🧹 Clearing old cache..."
php artisan config:clear
php artisan route:clear
php artisan view:clear

# migrate the database
echo "⚙️ Running migrations..."
php artisan migrate --force


echo "⚡ Caching config, routes, and views..."
# Clear existing configuration cache
php artisan config:cache
# Optimizing Route Loading - laravel docs
php artisan route:cache
# Optimizing View Loading - laravel docs
php artisan view:cache
php artisan event:cache

# exit the maintenance mode
echo "✅ Bringing app back up!"
php artisan up
echo "🚀 Deployment complete!"

#reload horizon if using horizon
#php artisan horizon:terminate

#restart the main supervisor if something goes wrong
#sudo service supervisor restart
