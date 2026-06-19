#!/usr/bin/env bash
set -euo pipefail
APP="/var/www/tonu-fashion-cms"
cd "$APP"

pkill -9 -f artisan 2>/dev/null || true

mkdir -p storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache
chmod -R 2775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache .env

# Remove root-owned cache files that block www-data from creating subdirs
find storage/framework/cache/data -type f -user root -delete 2>/dev/null || true
find storage/framework/cache/data -mindepth 1 -type d -empty -delete 2>/dev/null || true

sudo -u www-data php artisan optimize:clear
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

systemctl restart php8.4-fpm 2>/dev/null || systemctl restart php8.3-fpm 2>/dev/null || true

echo "=== ROUTE TESTS ==="
for path in "/" "/shop" "/admin/login"; do
  code=$(curl -sk -o /dev/null -w "%{http_code}" --max-time 30 "https://127.0.0.1${path}" -H "Host: tonu-fashion-cms.tonusoft.com")
  echo "${path} -> ${code}"
done

echo "FIX_CACHE_OK"
