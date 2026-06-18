#!/usr/bin/env bash
set -euo pipefail
APP_DIR="/var/www/tonu-fashion-cms"
DOMAIN="tonu-fashion-cms.tonusoft.com"
cd "$APP_DIR"

pkill -f "artisan storefront:warm-cache" 2>/dev/null || true

php artisan config:cache
php artisan route:cache
php artisan view:cache
chown -R www-data:www-data storage bootstrap/cache public/build

certbot --nginx -d "${DOMAIN}" --non-interactive --agree-tos -m admin@gmail.com --redirect 2>&1 || true
nginx -t && systemctl reload nginx

echo "HTTP:"
curl -sI "http://127.0.0.1/" -H "Host: ${DOMAIN}" | head -2
echo "HTTPS:"
curl -skI "https://127.0.0.1/" -H "Host: ${DOMAIN}" | head -3 || echo "no https"
echo "CONTENT:"
curl -s "http://127.0.0.1/" -H "Host: ${DOMAIN}" | grep -oE 'Featured Products|Shop by Category|theme-product-card' | head -6
echo "FINISH_OK"
