#!/bin/sh
set -e

# Ensure non-empty .env marker exists for legacy file_exists(base_path('.env')) checks
if [ ! -s /var/www/html/.env ]; then
    echo "APP_NAME=${APP_NAME:-AkMoney}" > /var/www/html/.env
fi

# Ensure storage directories exist with proper permissions
mkdir -p /var/www/html/storage/framework/cache/data \
         /var/www/html/storage/framework/sessions \
         /var/www/html/storage/framework/views \
         /var/www/html/storage/framework/testing \
         /var/www/html/storage/logs \
         /var/www/html/bootstrap/cache
chown -R www-data:www-data /var/www/html/storage /var/www/html/bootstrap/cache
chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

# Clear any stale pre-migration config cache before booting
rm -f /var/www/html/bootstrap/cache/config.php /var/www/html/bootstrap/cache/routes-v7.php

exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
