#!/bin/sh

# Exit on error
set -e

echo "Starting Backend Tasks..."

# Create runtime directory for Nginx if it doesn't exist
mkdir -p /run/nginx

# Ensure storage subdirectories exist and are writeable
mkdir -p /var/www/html/storage/framework/cache
mkdir -p /var/www/html/storage/framework/sessions
mkdir -p /var/www/html/storage/framework/views
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache

# Clear and Cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
echo "Running Migrations..."
php artisan migrate --force

# Start PHP-FPM in background
php-fpm -D

# Start Nginx in foreground
echo "Starting Nginx..."
nginx -g "daemon off;"
