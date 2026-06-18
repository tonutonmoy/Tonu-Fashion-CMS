#!/usr/bin/env bash
set -euo pipefail
APP_DIR="/var/www/tonu-fashion-cms"
cd "${APP_DIR}"
git fetch origin main
git reset --hard origin/main
rm -f bootstrap/cache/packages.php bootstrap/cache/services.php bootstrap/cache/config.php bootstrap/cache/routes-v7.php
mkdir -p bootstrap/cache storage/framework/{cache,sessions,views}
chown -R www-data:www-data storage bootstrap/cache .env
sudo -u www-data php artisan db:seed --class=Database\\Seeders\\DemoCatalogSeeder --force
sudo -u www-data php artisan optimize
systemctl restart php8.4-fpm
echo "SEED_OK"
