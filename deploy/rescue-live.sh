#!/usr/bin/env bash
set -euo pipefail
APP_DIR="/var/www/tonu-fashion-cms"
DOMAIN="tonu-fashion-cms.tonusoft.com"
cd "$APP_DIR"

echo "=== Kill stuck PHP/artisan ==="
pkill -9 -f "artisan" 2>/dev/null || true
pkill -9 -f "storefront:warm" 2>/dev/null || true
sleep 2
systemctl restart php8.4-fpm
systemctl reload nginx

echo "=== Warm homepage cache only (fast) ==="
php -d memory_limit=256M artisan optimize:clear
php -d memory_limit=256M -r "
require 'vendor/autoload.php';
\$app = require 'bootstrap/app.php';
\$kernel = \$app->make(Illuminate\Contracts\Console\Kernel::class);
\$kernel->bootstrap();
\$svc = \$app->make(App\Services\HomepageBuilderService::class);
\$svc->getInitialPageData();
\$svc->getSectionData('featured_products');
echo 'CACHE_WARMED';
"

php artisan config:cache
php artisan route:cache
php artisan view:cache
chown -R www-data:www-data storage bootstrap/cache

echo "=== Test ==="
curl -sk --max-time 45 "https://127.0.0.1/" -H "Host: ${DOMAIN}" -o /tmp/home.html -w "HTTP:%{http_code} TIME:%{time_total}s\n"
grep -oE 'Featured Products|Shop by Category|Demo Product|Fashion BD' /tmp/home.html | head -6
echo "RESCUE_OK"
