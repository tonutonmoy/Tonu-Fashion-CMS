#!/usr/bin/env bash
set -euo pipefail
APP="/var/www/tonu-fashion-cms"
DOMAIN="tonu-fashion-cms.tonusoft.com"
SITE="/etc/nginx/sites-available/${DOMAIN}"

pkill -9 -f artisan 2>/dev/null || true
sleep 1

grep -q PERFORMANCE_PROFILING "$APP/.env" || echo "PERFORMANCE_PROFILING=false" >> "$APP/.env"
sed -i 's/PERFORMANCE_PROFILING=true/PERFORMANCE_PROFILING=false/' "$APP/.env"

if ! grep -q fastcgi_read_timeout "$SITE"; then
  sed -i '/fastcgi_hide_header/a\        fastcgi_read_timeout 300s;' "$SITE"
fi

nginx -t && systemctl reload nginx
systemctl restart php8.4-fpm

cd "$APP"
php artisan config:clear
php artisan config:cache

nohup php -d memory_limit=256M artisan storefront:warm-cache > /tmp/warm.log 2>&1 &
echo "Warm PID: $!"

# Wait for warm to finish (max 3 min)
for i in $(seq 1 36); do
  if ! pgrep -f "storefront:warm-cache" >/dev/null; then
    echo "Warm finished"
    break
  fi
  sleep 5
done

curl -sk --max-time 90 "https://127.0.0.1/" -H "Host: ${DOMAIN}" -o /tmp/home.html -w "HOME:%{http_code} %{time_total}s\n" || echo "CURL_TIMEOUT"
grep -oE 'Featured Products|Shop by Category|Demo Product|Fashion BD' /tmp/home.html 2>/dev/null | head -5 || true
tail -3 /tmp/warm.log 2>/dev/null || true
echo "RESCUE2_OK"
