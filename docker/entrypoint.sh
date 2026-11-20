#!/bin/sh
set -e

# Cache configuration if in production and APP_KEY is set
if [ "$APP_ENV" = "production" ] && [ -n "$APP_KEY" ]; then
    echo "Caching Laravel configuration and routes..."
    php artisan config:cache || true
    php artisan route:cache || true
fi

# Ensure storage directories exist with proper permissions
mkdir -p /var/www/html/storage/framework/{cache/data,sessions,views,testing} /var/www/html/storage/logs
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
