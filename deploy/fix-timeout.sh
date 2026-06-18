#!/usr/bin/env bash
set -euo pipefail
APP_DIR="/var/www/tonu-fashion-cms"
DOMAIN="tonu-fashion-cms.tonusoft.com"
cd "$APP_DIR"

pkill -f "artisan storefront:warm-cache" 2>/dev/null || true
pkill -f "artisan mongo" 2>/dev/null || true

# Nginx fastcgi timeout for slow Atlas first hit
SITE="/etc/nginx/sites-available/${DOMAIN}"
if ! grep -q fastcgi_read_timeout "$SITE"; then
  sed -i '/fastcgi_hide_header/a\        fastcgi_read_timeout 120s;' "$SITE"
fi
nginx -t && systemctl reload nginx
systemctl restart php8.4-fpm

php artisan optimize:clear
php -d memory_limit=512M artisan tinker --execute="App\Services\HomepageBuilderService::class; app(App\Services\HomepageBuilderService::class)->getInitialPageData(); echo 'INITIAL_OK';" 2>/dev/null || \
  php -d memory_limit=512M -r "require 'vendor/autoload.php'; \$a=require 'bootstrap/app.php'; \$a->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); app('App\Services\HomepageBuilderService')->getInitialPageData(); echo 'INITIAL_OK';"

php artisan config:cache
php artisan route:cache
php artisan view:cache
chown -R www-data:www-data storage bootstrap/cache

echo "LOCAL TEST:"
curl -sk --max-time 60 "https://127.0.0.1/" -H "Host: ${DOMAIN}" | grep -oE 'Featured Products|Shop by Category|Demo Product' | head -5
curl -skI --max-time 10 "https://127.0.0.1/" -H "Host: ${DOMAIN}" | head -3
echo "TIMEOUT_FIX_OK"
