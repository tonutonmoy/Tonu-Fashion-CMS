#!/usr/bin/env bash
# Quick health check for Hostinger / MySQL deployment
set -euo pipefail
APP_DIR="${1:-$(cd "$(dirname "$0")/.." && pwd)}"
cd "$APP_DIR"

echo "=== PHP ==="
php -v | head -1

echo "=== MySQL connection ==="
php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'MYSQL_OK'; } catch (Throwable \$e) { echo 'MYSQL_FAIL: '.\$e->getMessage(); }" 2>/dev/null || \
php -r "require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap(); try { Illuminate\Support\Facades\DB::connection()->getPdo(); echo 'MYSQL_OK'; } catch (Throwable \$e) { echo 'MYSQL_FAIL: '.\$e->getMessage(); }"

echo ""
echo "=== Storage link ==="
test -L public/storage && echo "LINK_OK" || echo "LINK_MISSING (run php artisan storage:link)"

echo "=== Cache writable ==="
test -w storage/framework/cache && echo "CACHE_OK" || echo "CACHE_NOT_WRITABLE"

echo "=== Queue driver ==="
grep -E '^QUEUE_CONNECTION=' .env 2>/dev/null || echo "QUEUE_CONNECTION not set"
