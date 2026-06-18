#!/usr/bin/env bash
set -euo pipefail
APP_DIR="/var/www/tonu-fashion-cms"
DOMAIN="tonu-fashion-cms.tonusoft.com"
cd "$APP_DIR"

export COMPOSER_ALLOW_SUPERUSER=1
export NODE_OPTIONS="--max-old-space-size=512"

echo "=== Git pull ==="
git fetch origin main
git reset --hard origin/main

echo "=== Build assets ==="
npm ci --no-audit --no-fund 2>/dev/null || npm install --no-audit --no-fund
npm run build

echo "=== Laravel cache ==="
php -d memory_limit=512M artisan optimize:clear
php -d memory_limit=512M artisan storefront:warm-cache --no-interaction
php artisan config:cache
php artisan route:cache
php artisan view:cache
chown -R www-data:www-data storage bootstrap/cache public/build

echo "=== SSL ==="
ufw allow 80/tcp 2>/dev/null || true
ufw allow 443/tcp 2>/dev/null || true
if ! certbot certificates 2>/dev/null | grep -q "${DOMAIN}"; then
  certbot --nginx -d "${DOMAIN}" --non-interactive --agree-tos -m admin@gmail.com --redirect || \
    certbot certonly --webroot -w "${APP_DIR}/public" -d "${DOMAIN}" --non-interactive --agree-tos -m admin@gmail.com
  certbot --nginx -d "${DOMAIN}" --non-interactive --redirect 2>/dev/null || true
fi

nginx -t && systemctl reload nginx

echo "=== Verify ==="
curl -sI "http://127.0.0.1/" -H "Host: ${DOMAIN}" | head -3
curl -skI "https://127.0.0.1/" -H "Host: ${DOMAIN}" | head -3 || echo "HTTPS pending"
curl -s "http://127.0.0.1/" -H "Host: ${DOMAIN}" | grep -o 'Featured Products\|Shop by Category\|theme-product-card' | head -5

echo "QUICK_UPDATE_OK"
