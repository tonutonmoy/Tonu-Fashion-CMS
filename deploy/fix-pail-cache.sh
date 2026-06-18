#!/usr/bin/env bash
set -euo pipefail
APP_DIR="/var/www/tonu-fashion-cms"
cd "${APP_DIR}"
rm -f bootstrap/cache/packages.php bootstrap/cache/services.php bootstrap/cache/config.php bootstrap/cache/routes-v7.php
chown -R www-data:www-data storage bootstrap/cache .env
chmod -R 775 storage bootstrap/cache
sudo -u www-data php artisan package:discover --ansi
sudo -u www-data php artisan config:cache
echo "FIX_OK"
