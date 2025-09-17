#!/bin/bash

# Create log directories
mkdir -p /var/log/supervisor

# Wait for database to be ready (if using external database)
echo "Waiting for database connection..."
php artisan migrate --force

# Clear and cache configuration
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Create storage link if it doesn't exist
php artisan storage:link

# Set proper permissions
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Start supervisor to manage processes
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
