#!/usr/bin/env bash
set -euo pipefail
DOMAIN="tonu-fashion-cms.tonusoft.com"
SITE="/etc/nginx/sites-available/${DOMAIN}"

echo "==> PHP OPcache"
PHP_INI=$(php -r 'echo php_ini_loaded_file();' 2>/dev/null || true)
if [ -n "${PHP_INI}" ] && [ -f "${PHP_INI}" ]; then
  sed -i 's/^;*opcache.enable=.*/opcache.enable=1/' "${PHP_INI}" 2>/dev/null || true
  sed -i 's/^;*opcache.memory_consumption=.*/opcache.memory_consumption=128/' "${PHP_INI}" 2>/dev/null || true
  sed -i 's/^;*opcache.interned_strings_buffer=.*/opcache.interned_strings_buffer=16/' "${PHP_INI}" 2>/dev/null || true
  sed -i 's/^;*opcache.max_accelerated_files=.*/opcache.max_accelerated_files=20000/' "${PHP_INI}" 2>/dev/null || true
  sed -i 's/^;*opcache.validate_timestamps=.*/opcache.validate_timestamps=0/' "${PHP_INI}" 2>/dev/null || true
  echo "  OPcache tuned in ${PHP_INI}"
fi

if [ -f "$SITE" ]; then
  echo "==> Nginx static asset cache"
  if ! grep -q 'location /build/' "$SITE"; then
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
    echo "  Added /build/ and /themes/ cache blocks"
  else
    echo "  Static blocks already present"
  fi

  echo "==> Nginx gzip"
  if ! grep -q 'gzip on' "$SITE"; then
    sed -i '/server_name/i\
    gzip on;\
    gzip_vary on;\
    gzip_proxied any;\
    gzip_comp_level 5;\
    gzip_min_length 256;\
    gzip_types text/plain text/css application/json application/javascript text/xml application/xml image/svg+xml application/font-woff application/font-woff2;\
' "$SITE"
    echo "  gzip enabled"
  else
    echo "  gzip already enabled"
  fi

  nginx -t
  systemctl reload nginx
else
  echo "WARN: nginx site not found: $SITE"
fi

systemctl restart php8.4-fpm 2>/dev/null || systemctl restart php8.3-fpm 2>/dev/null || true
echo "OPTIMIZE_SERVER_OK"
