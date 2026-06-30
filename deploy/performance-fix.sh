#!/usr/bin/env bash
# One-shot production performance tune (run on VPS via: python deploy/ssh_run.py performance-fix.sh)
set -euo pipefail
APP="/var/www/tonu-fashion-cms"
cd "$APP"

set_env() {
  local key="$1" val="$2"
  if grep -q "^${key}=" .env; then
    sed -i "s|^${key}=.*|${key}=${val}|" .env
  else
    echo "${key}=${val}" >> .env
  fi
}

echo "==> .env performance flags"
set_env APP_ENV production
set_env APP_DEBUG false
set_env STOREFRONT_HTML_CACHE true
set_env STOREFRONT_CACHE_TTL 7200
set_env STOREFRONT_SYSTEM_FONTS true
set_env PERFORMANCE_PROFILING false

if command -v redis-cli >/dev/null 2>&1 && php -m 2>/dev/null | grep -qi '^redis$'; then
  set_env CACHE_STORE redis
  set_env REDIS_HOST 127.0.0.1
  set_env REDIS_PORT 6379
else
  set_env CACHE_STORE file
fi

echo "==> Composer + assets"
composer install --no-dev --optimize-autoloader --no-interaction --ignore-platform-reqs
npm ci --no-audit --no-fund 2>/dev/null || npm install --no-audit --no-fund
npm run build

echo "==> Laravel caches"
sudo -u www-data php artisan optimize:clear
sudo -u www-data php -d memory_limit=512M artisan storefront:warm-cache --no-interaction
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
sudo -u www-data php artisan optimize

bash "$APP/deploy/optimize-server.sh" 2>/dev/null || true
systemctl restart php8.4-fpm 2>/dev/null || systemctl restart php8.3-fpm 2>/dev/null || true
systemctl reload nginx 2>/dev/null || true

echo "PERFORMANCE_FIX_OK"
