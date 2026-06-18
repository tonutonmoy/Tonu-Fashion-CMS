#!/usr/bin/env bash
set -euo pipefail
APP="/var/www/tonu-fashion-cms"
DOMAIN="tonu-fashion-cms.tonusoft.com"
cd "$APP"

pkill -9 -f artisan 2>/dev/null || true
sleep 1

git fetch origin main
git reset --hard origin/main

mkdir -p storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
# Ensure file cache can create nested hash directories
find storage/framework/cache/data -type d -exec chmod 775 {} \; 2>/dev/null || true

grep -q PERFORMANCE_PROFILING .env || echo "PERFORMANCE_PROFILING=false" >> .env
sed -i 's/PERFORMANCE_PROFILING=true/PERFORMANCE_PROFILING=false/' .env

PHP_INI="/etc/php/8.4/fpm/php.ini"
sed -i 's/^max_execution_time = .*/max_execution_time = 120/' "$PHP_INI" 2>/dev/null || true
sed -i 's/^memory_limit = .*/memory_limit = 256M/' "$PHP_INI" 2>/dev/null || true

php artisan optimize:clear
php -d memory_limit=256M artisan storefront:warm-cache --no-interaction || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

systemctl restart php8.4-fpm
systemctl reload nginx

echo "=== ROUTE TESTS ==="
for path in "/" "/admin/login" "/admin" "/shop" "/api/cart" "/api/bd/divisions" "/sitemap.xml"; do
  code=$(curl -sk -o /dev/null -w "%{http_code}" --max-time 30 "https://127.0.0.1${path}" -H "Host: ${DOMAIN}")
  echo "${path} -> ${code}"
done

echo "FIX_ALL_OK"
