#!/usr/bin/env bash
set -euo pipefail
APP="/var/www/tonu-fashion-cms"
cd "$APP"
git fetch origin main
git reset --hard origin/main
DB_USER="${DB_USER:-tonu_fashion}"
DB_PASS="${DB_PASS:-76676239168794ed798da19bde0a31f9}"
DB_NAME="${DB_NAME:-tonu_fashion_cms}"

PRODUCT_COUNT=$(mysql -u"${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" -N -e "SELECT COUNT(*) FROM products;" 2>/dev/null || echo "0")
MENU_COUNT=$(mysql -u"${DB_USER}" -p"${DB_PASS}" "${DB_NAME}" -N -e "SELECT COUNT(*) FROM menu_items;" 2>/dev/null || echo "0")

echo "products=${PRODUCT_COUNT} menu_items=${MENU_COUNT}"

if [ "${PRODUCT_COUNT}" -lt 5 ] || [ "${MENU_COUNT}" -lt 3 ]; then
  echo "Seeding demo data..."
  sudo -u www-data php artisan db:seed --force
fi

sudo -u www-data php artisan optimize:clear
sudo -u www-data php -d memory_limit=512M artisan storefront:warm-cache --no-interaction
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache

systemctl restart php8.4-fpm 2>/dev/null || true

for path in "/" "/shop" "/admin/login"; do
  code=$(curl -sk -o /dev/null -w "%{http_code}" --max-time 30 "https://127.0.0.1${path}" -H "Host: tonu-fashion-cms.tonusoft.com")
  cache=$(curl -sk -I --max-time 30 "https://127.0.0.1${path}" -H "Host: tonu-fashion-cms.tonusoft.com" | grep -i x-storefront-cache || true)
  echo "${path} -> ${code} ${cache}"
done

echo "RESCUE_DATA_OK"
