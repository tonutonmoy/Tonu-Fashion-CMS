#!/usr/bin/env bash
APP="/var/www/tonu-fashion-cms"
cd "$APP"

echo "=== ADMIN ERROR LOG ==="
tail -80 storage/logs/laravel.log 2>/dev/null | tail -40

echo "=== TEST ROUTES ==="
for path in "/" "/admin" "/admin/login" "/shop" "/api/cart"; do
  code=$(curl -sk -o /dev/null -w "%{http_code}" --max-time 30 "https://127.0.0.1${path}" -H "Host: tonu-fashion-cms.tonusoft.com")
  echo "${path} -> ${code}"
done

echo "=== PHP-FPM ==="
systemctl is-active php8.4-fpm nginx
pgrep -c php-fpm || true

echo "=== PERMS ==="
ls -la storage/logs/laravel.log bootstrap/cache/ 2>/dev/null | head -5

echo "DIAG_ADMIN_DONE"
