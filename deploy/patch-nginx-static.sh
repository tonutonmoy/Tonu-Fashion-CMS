#!/usr/bin/env bash
set -euo pipefail
DOMAIN="tonu-fashion-cms.tonusoft.com"
SITE="/etc/nginx/sites-available/${DOMAIN}"

if [ ! -f "$SITE" ]; then
  echo "Site config not found: $SITE"
  exit 1
fi

if grep -q 'location /build/' "$SITE"; then
  echo "Static cache blocks already present"
else
  sed -i '/location \/ {/i\
    location /build/ {\
        expires 1y;\
        add_header Cache-Control "public, immutable";\
        access_log off;\
    }\
\
    location ~* ^/themes/.*\\.(css|js|woff2?|svg)$ {\
        expires 30d;\
        add_header Cache-Control "public";\
        access_log off;\
    }\
' "$SITE"
  echo "Added static asset cache blocks"
fi

nginx -t
systemctl reload nginx
echo "NGINX_STATIC_OK"
