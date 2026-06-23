#!/usr/bin/env bash
set -euo pipefail
APP="/var/www/tonu-fashion-cms"
cd "$APP"
git fetch origin main
git reset --hard origin/main

DB_NAME="${DB_NAME:-tonu_fashion_cms}"
DB_USER="${DB_USER:-tonu_fashion}"
DB_PASS="${DB_PASS:-76676239168794ed798da19bde0a31f9}"

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

if command -v redis-cli >/dev/null 2>&1; then
  set_env CACHE_STORE redis
  set_env SESSION_DRIVER redis
  set_env REDIS_HOST 127.0.0.1
  set_env REDIS_PORT 6379
else
  apt-get update -qq && apt-get install -y -qq redis-server >/dev/null 2>&1 || true
  systemctl enable redis-server 2>/dev/null || true
  systemctl start redis-server 2>/dev/null || true
  if command -v redis-cli >/dev/null 2>&1; then
    set_env CACHE_STORE redis
    set_env SESSION_DRIVER redis
    set_env REDIS_HOST 127.0.0.1
    set_env REDIS_PORT 6379
  fi
fi

set_env IMAGE_DRIVER auto
set_env IMAGEBB_API_URL "https://api.imgbb.com/1/upload"
if [ -n "${IMAGEBB_API_KEY:-}" ]; then
  set_env IMAGEBB_API_KEY "${IMAGEBB_API_KEY}"
elif ! grep -q "^IMAGEBB_API_KEY=." .env 2>/dev/null; then
  echo "WARN: IMAGEBB_API_KEY not set on server .env"
fi

npm run build
sudo -u www-data php artisan migrate --force --no-interaction
mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs
chown -R www-data:www-data public/build storage bootstrap/cache .env
chmod -R 2775 storage bootstrap/cache
rm -f bootstrap/cache/config.php bootstrap/cache/routes-v7.php bootstrap/cache/events.php
sudo -u www-data php artisan optimize:clear
chown -R www-data:www-data storage bootstrap/cache
sudo -u www-data php -d memory_limit=512M artisan storefront:warm-cache --no-interaction
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
systemctl restart php8.4-fpm 2>/dev/null || true
echo "DEPLOY_MOBILE_OK"
