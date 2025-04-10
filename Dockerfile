# Use official PHP 8.2 base image with FPM
FROM php:8.2-fpm

# Set working directory
WORKDIR /var/www

# Install system dependencies
RUN apt-get update && apt-get install -y \
    git \
    curl \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    libpq-dev \
    && docker-php-ext-install pdo pdo_pgsql mbstring zip bcmath exif pcntl gd

# Install Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Copy the application code
COPY . .

# Set correct permissions
RUN chown -R www-data:www-data /var/www \
    && chmod -R 755 /var/www

# Install Laravel dependencies
RUN composer install --no-dev --optimize-autoloader

# Expose port used by Laravel and Render
EXPOSE 10000

# Run Laravel setup commands
CMD php artisan key:generate && \
    php artisan migrate --force && \
    php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    php artisan serve --host=0.0.0.0 --port=10000
