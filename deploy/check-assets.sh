#!/usr/bin/env bash
DOMAIN="tonu-fashion-cms.tonusoft.com"
APP="/var/www/tonu-fashion-cms"
SITE="/etc/nginx/sites-available/${DOMAIN}"

echo "=== STUCK PROCESSES ==="
pgrep -af "artisan|warm-cache" || echo "none"

echo "=== FASTCGI TIMEOUT ==="
grep fastcgi_read_timeout "$SITE" || echo "MISSING"

echo "=== BUILD MANIFEST ==="
ls -la "$APP/public/build/manifest.json" 2>/dev/null || echo "NO MANIFEST"
head -5 "$APP/public/build/manifest.json" 2>/dev/null

echo "=== EXTRACT ASSETS FROM HOME HTML ==="
HTML=$(curl -sk --max-time 15 "https://127.0.0.1/" -H "Host: ${DOMAIN}")
echo "$HTML" | grep -oE '/build/assets/[^\" ]+' | sort -u | head -10

echo "=== ASSET HTTP CODES ==="
for asset in $(echo "$HTML" | grep -oE '/build/assets/[^\" ]+' | sort -u | head -8); do
  code=$(curl -sk -o /dev/null -w "%{http_code}" --max-time 10 "https://127.0.0.1${asset}" -H "Host: ${DOMAIN}")
  echo "${asset} -> ${code}"
done

echo "ASSET_CHECK_OK"
