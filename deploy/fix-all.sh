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
PHP_POOL="/etc/php/8.4/fpm/pool.d/www.conf"
sed -i 's/^max_execution_time = .*/max_execution_time = 120/' "$PHP_INI" 2>/dev/null || true
sed -i 's/^memory_limit = .*/memory_limit = 256M/' "$PHP_INI" 2>/dev/null || true
sed -i 's/^pm = .*/pm = dynamic/' "$PHP_POOL" 2>/dev/null || true
sed -i 's/^pm.max_children = .*/pm.max_children = 8/' "$PHP_POOL" 2>/dev/null || true
sed -i 's/^;*pm.start_servers = .*/pm.start_servers = 2/' "$PHP_POOL" 2>/dev/null || true
sed -i 's/^;*pm.min_spare_servers = .*/pm.min_spare_servers = 2/' "$PHP_POOL" 2>/dev/null || true
sed -i 's/^;*pm.max_spare_servers = .*/pm.max_spare_servers = 4/' "$PHP_POOL" 2>/dev/null || true

npm run build 2>/dev/null || true
chown -R www-data:www-data public/build 2>/dev/null || true

php artisan optimize:clear
php artisan config:cache
php artisan route:cache
php artisan view:cache

systemctl restart php8.4-fpm
systemctl reload nginx

# Warm cache in background so the site stays reachable during deploy
nohup php -d memory_limit=512M artisan storefront:warm-cache --no-interaction > /tmp/warm-cache.log 2>&1 &

SITE="/etc/nginx/sites-available/${DOMAIN}"
if [ -f "$SITE" ] && ! grep -q 'location /build/' "$SITE"; then
  sed -i '/location \/ {/i\
    location /build/ {\
        expires 1y;\
        add_header Cache-Control "public, immutable";\
        access_log off;\
    }\
\
    location ~* ^/themes/.*\\.(css|js|woff2?|svg)$ {\
        expires 30d;\
        add_header Cache-Control "public";\
        access_log off;\
    }\
' "$SITE"
  nginx -t && systemctl reload nginx
fi

echo "=== ROUTE TESTS ==="
for path in "/" "/admin/login" "/admin" "/shop" "/api/cart" "/api/bd/divisions" "/sitemap.xml"; do
  code=$(curl -sk -o /dev/null -w "%{http_code}" --max-time 30 "https://127.0.0.1${path}" -H "Host: ${DOMAIN}")
  echo "${path} -> ${code}"
done

echo "FIX_ALL_OK"
