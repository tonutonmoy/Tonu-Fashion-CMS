#!/usr/bin/env bash
# Hostinger / shared hosting deploy (MySQL + file cache, no Mongo/Redis/Node at runtime)
set -euo pipefail

APP_DIR="${1:-$(pwd)}"
cd "$APP_DIR"

echo "==> Composer install (no dev)"
composer install --no-dev --optimize-autoloader --no-interaction

echo "==> Migrations"
php artisan migrate --force

echo "==> Storage link"
php artisan storage:link 2>/dev/null || true

echo "==> Optimize"
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "==> Warm cache (background)"
nohup php artisan storefront:warm-cache >> storage/logs/warm-cache.log 2>&1 &

echo "==> Done. Set cron: * * * * * php artisan schedule:run"
