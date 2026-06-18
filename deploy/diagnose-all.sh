#!/usr/bin/env bash
set -euo pipefail
APP_DIR="/var/www/tonu-fashion-cms"
cd "$APP_DIR"

echo "=== SERVICES ==="
systemctl is-active nginx php8.4-fpm 2>/dev/null || systemctl is-active nginx php*-fpm

echo "=== MONGODB PING ==="
php artisan tinker --execute="try { DB::connection('mongodb')->getMongoDB()->command(['ping'=>1]); echo 'MONGO_OK'; } catch (Throwable \$e) { echo 'MONGO_FAIL: '.\$e->getMessage(); }" 2>/dev/null || \
php -r "require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); try { Illuminate\Support\Facades\DB::connection('mongodb')->getMongoDB()->command(['ping'=>1]); echo 'MONGO_OK'; } catch (Throwable \$e) { echo 'MONGO_FAIL: '.\$e->getMessage(); }"

echo ""
echo "=== COUNTS ==="
php artisan tinker --execute="echo 'products='.App\Models\Product::count().' categories='.App\Models\Category::count().' sections='.App\Models\HomepageSection::where('enabled',true)->count();" 2>/dev/null || true

echo ""
echo "=== ENV (masked) ==="
grep -E '^(APP_URL|DB_CONNECTION|MONGODB_DATABASE|CACHE_STORE|APP_DEBUG)=' .env

echo ""
echo "=== PUBLIC BUILD ==="
ls -la public/build/ 2>/dev/null | head -5 || echo "NO BUILD"
ls public/build/assets/*storefront* 2>/dev/null | head -3 || echo "NO STOREFRONT JS"

echo ""
echo "=== HOME SECTION API ==="
curl -s "http://127.0.0.1/home/section/featured_products" -H "Host: tonu-fashion-cms.tonusoft.com" -H "Accept: application/json" | head -c 300
echo ""

echo ""
echo "=== SSL ==="
certbot certificates 2>/dev/null || echo "no certs"
ss -tlnp | grep ':443' || echo "443 not listening"
