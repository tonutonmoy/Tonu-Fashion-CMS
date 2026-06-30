#!/usr/bin/env bash
set -euo pipefail
DOMAIN="tonu-fashion-cms.tonusoft.com"
SITE="/etc/nginx/sites-available/${DOMAIN}"

if [ ! -f "$SITE" ]; then
  echo "Site config not found: $SITE"
  exit 1
fi

# Server-level add_header hides FastCGI response headers (X-Storefront-Cache).
sed -i '/add_header X-Frame-Options "SAMEORIGIN";/d' "$SITE"
sed -i '/add_header X-Content-Type-Options "nosniff";/d' "$SITE"

if ! grep -q 'fastcgi_pass_header X-Storefront-Cache' "$SITE"; then
  sed -i '/fastcgi_pass unix:/i\        fastcgi_pass_header X-Storefront-Cache;\n        fastcgi_pass_header X-Response-Time;' "$SITE"
fi

nginx -t
systemctl reload nginx
echo "NGINX_PASS_HEADERS_OK"
