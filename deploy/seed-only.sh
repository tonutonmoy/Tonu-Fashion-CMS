#!/usr/bin/env bash
set -euo pipefail
APP_DIR="/var/www/tonu-fashion-cms"
cd "${APP_DIR}"
mkdir -p bootstrap/cache storage/framework/{cache,sessions,views}
chown -R www-data:www-data storage bootstrap/cache .env
chmod -R 775 storage bootstrap/cache
sudo -u www-data php artisan db:seed --class=Database\\Seeders\\DemoCatalogSeeder --force
sudo -u www-data php artisan package:discover --ansi
sudo -u www-data php artisan optimize
systemctl restart php8.4-fpm
mysql -u tonu_fashion -p76676239168794ed798da19bde0a31f9 tonu_fashion_cms -e "SELECT COUNT(*) AS products FROM products;"
echo "DONE"
