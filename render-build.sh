#!/usr/bin/env bash

# Exit on error
set -o errexit

# Install PHP dependencies
composer install --no-dev --optimize-autoloader

# Generate application key if not set
if [ -z "$APP_KEY" ]; then
  php artisan key:generate --force
fi

# Clear and optimize application
php artisan optimize:clear
php artisan optimize

# Build assets (if you have frontend assets)
if [ -f "package.json" ]; then
  npm install
  npm run build
fi

# Set proper permissions for Laravel
chmod -R 775 storage bootstrap/cache