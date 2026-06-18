#!/usr/bin/env bash
set -euo pipefail
DOMAIN="tonu-fashion-cms.tonusoft.com"
APP="/var/www/tonu-fashion-cms"

echo "=== SERVICES ==="
systemctl is-active nginx php8.4-fpm || true
free -h | head -2

echo "=== LOCAL CURL ==="
for path in "/" "/shop" "/up"; do
  code=$(curl -sk -o /dev/null -w "%{http_code} %{time_total}s" --max-time 20 "https://127.0.0.1${path}" -H "Host: ${DOMAIN}" || echo "fail")
  echo "${path} -> ${code}"
done

echo "=== PHP-FPM / ARTISAN ==="
pgrep -af "php-fpm|artisan" | head -10 || true

echo "=== RECENT ERRORS ==="
tail -15 "$APP/storage/logs/laravel.log" 2>/dev/null || true

echo "=== NGINX ERROR ==="
tail -10 /var/log/nginx/error.log 2>/dev/null || true

echo "DIAG_OK"
