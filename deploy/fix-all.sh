#!/usr/bin/env bash
set -euo pipefail
APP="/var/www/tonu-fashion-cms"
DOMAIN="tonu-fashion-cms.tonusoft.com"
cd "$APP"

pkill -9 -f artisan 2>/dev/null || true
sleep 1

git fetch origin main
git reset --hard origin/main

export COMPOSER_ALLOW_SUPERUSER=1
composer install --no-dev --optimize-autoloader --no-interaction

# Ensure MySQL is running
mkdir -p /var/lib/mysql-files
chown mysql:mysql /var/lib/mysql-files 2>/dev/null || true
systemctl start mysql 2>/dev/null || true

DB_NAME="${DB_NAME:-tonu_fashion_cms}"
DB_USER="${DB_USER:-tonu_fashion}"
DB_PASS="${DB_PASS:-76676239168794ed798da19bde0a31f9}"

mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;" 2>/dev/null || true
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';" 2>/dev/null || true
mysql -e "ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';" 2>/dev/null || true
mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost'; FLUSH PRIVILEGES;" 2>/dev/null || true

# Ensure MySQL credentials in .env
set_env() {
  local key="$1" val="$2"
  if grep -q "^${key}=" .env; then
    sed -i "s|^${key}=.*|${key}=${val}|" .env
  else
    echo "${key}=${val}" >> .env
  fi
}

set_env DB_CONNECTION mysql
set_env DB_HOST 127.0.0.1
set_env DB_PORT 3306
set_env DB_DATABASE "${DB_NAME}"
set_env DB_USERNAME "${DB_USER}"
set_env DB_PASSWORD "${DB_PASS}"
set_env QUEUE_CONNECTION database
set_env IMAGE_DRIVER local
set_env CACHE_STORE file
set_env SESSION_DRIVER file

mkdir -p storage/framework/{cache/data,sessions,views} storage/logs bootstrap/cache
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache
# Ensure file cache can create nested hash directories
find storage/framework/cache/data -type d -exec chmod 775 {} \; 2>/dev/null || true

grep -q PERFORMANCE_PROFILING .env || echo "PERFORMANCE_PROFILING=false" >> .env
sed -i 's/PERFORMANCE_PROFILING=true/PERFORMANCE_PROFILING=false/' .env
grep -q STOREFRONT_SYSTEM_FONTS .env || echo "STOREFRONT_SYSTEM_FONTS=true" >> .env
sed -i 's/STOREFRONT_SYSTEM_FONTS=false/STOREFRONT_SYSTEM_FONTS=true/' .env
grep -q STOREFRONT_HTML_CACHE .env || echo "STOREFRONT_HTML_CACHE=true" >> .env

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

# First deploy after MySQL migration: fresh migrate + seed if products table empty
PRODUCT_COUNT=$(mysql -u"${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" -N -e "SELECT COUNT(*) FROM products;" 2>/dev/null || echo "0")
if [ "${PRODUCT_COUNT}" = "0" ] || [ "${PRODUCT_COUNT}" = "" ]; then
  php -d memory_limit=512M artisan app:install-database --fresh --no-interaction || \
    php -d memory_limit=512M artisan migrate:fresh --seed --force
else
  php artisan migrate --force
fi

php artisan storage:link 2>/dev/null || true
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

bash /root/setup-cron.sh 2>/dev/null || {
  CRON_LINE="* * * * * cd ${APP} && php artisan schedule:run >> /dev/null 2>&1"
  (crontab -l 2>/dev/null | grep -Fv "artisan schedule:run"; echo "$CRON_LINE") | crontab - 2>/dev/null || true
}

echo "FIX_ALL_OK"
