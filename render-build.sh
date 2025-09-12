#!/usr/bin/env bash

# Exit on error
set -o errexit

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force
fi

# Install and build frontend assets
echo "Installing Node.js dependencies..."
npm install

echo "Building frontend assets..."
npm run build

# Clear and optimize application
php artisan optimize:clear
php artisan optimize

# Set proper permissions for Laravel
chmod -R 775 storage bootstrap/cache