#!/bin/bash

# Laravel Deployment Script for finance.syhb.site
# This script handles deployment tasks including Livewire assets and caching

echo "Starting deployment..."

# Copy Livewire assets to public directory (for nginx compatibility)
echo "Copying Livewire assets..."
mkdir -p public/vendor/livewire
cp -r vendor/livewire/livewire/dist/* public/vendor/livewire/

# Install/update dependencies
echo "Installing dependencies..."
composer dump-autoload --optimize

# Publish Livewire assets
echo "Publishing Livewire assets..."
php artisan vendor:publish --force --tag=livewire:assets

# Publish Filament assets
echo "Publishing Filament assets..."
php artisan filament:assets

# Cache Filament components
echo "Caching Filament components..."
php artisan filament:cache-components

# Clear all caches first
echo "Clearing caches..."
php artisan config:clear
php artisan cache:clear
php artisan route:clear
php artisan view:clear

# Build optimized caches
echo "Building optimized caches..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Set proper permissions
# echo "Setting permissions..."
# chmod -R 755 storage bootstrap/cache
# chown -R www-data:www-data storage bootstrap/cache public/vendor

# Reload nginx (if needed)
# if command -v nginx &> /dev/null; then
#     echo "Reloading nginx..."
#     nginx -s reload
# fi

echo "Deployment completed successfully!"
echo "Don't forget to check your .env file for production settings:"
echo "  - APP_URL=https://finance.syhb.site"
echo "  - APP_ENV=production"
echo "  - APP_DEBUG=false"
echo "  - SESSION_SECURE_COOKIE=true"