#!/usr/bin/env bash
set -euo pipefail
APP_DIR="/var/www/tonu-fashion-cms"
cd "${APP_DIR}"
chown -R www-data:www-data storage bootstrap/cache .env
chmod -R 775 storage bootstrap/cache
chmod 640 .env
sudo -u www-data php artisan config:clear
sudo -u www-data php artisan config:cache
echo "CACHE_OK"
