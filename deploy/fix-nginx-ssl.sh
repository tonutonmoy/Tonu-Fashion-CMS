#!/usr/bin/env bash
set -euo pipefail
DOMAIN="tonu-fashion-cms.tonusoft.com"
APP_DIR="/var/www/tonu-fashion-cms"
API="/etc/nginx/sites-available/api.tonusoft.com"

# Remove duplicate proxy_cache_path from api vhost (already in nginx.conf)
grep -v '^proxy_cache_path' "$API" > /tmp/api.tonusoft.com && mv /tmp/api.tonusoft.com "$API"
nginx -t

certbot certonly --webroot -w "${APP_DIR}/public" -d "${DOMAIN}" \
  --non-interactive --agree-tos -m admin@gmail.com --force-renewal 2>&1 || \
certbot certonly --webroot -w "${APP_DIR}/public" -d "${DOMAIN}" \
  --non-interactive --agree-tos -m admin@gmail.com 2>&1 || true

PHP_SOCK="/var/run/php/php8.4-fpm.sock"
if [ -f "/etc/letsencrypt/live/${DOMAIN}/fullchain.pem" ]; then
  cat > "/etc/nginx/sites-available/${DOMAIN}" <<NGINX
server {
    listen 80;
    listen [::]:80;
    server_name ${DOMAIN};
    return 301 https://\$host\$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name ${DOMAIN};
    root ${APP_DIR}/public;

    ssl_certificate /etc/letsencrypt/live/${DOMAIN}/fullchain.pem;
    ssl_certificate_key /etc/letsencrypt/live/${DOMAIN}/privkey.pem;
    include /etc/letsencrypt/options-ssl-nginx.conf;
    ssl_dhparam /etc/letsencrypt/ssl-dhparams.pem;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";
    index index.php;
    charset utf-8;

    location / { try_files \$uri \$uri/ /index.php?\$query_string; }
    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt { access_log off; try_files \$uri /index.php?\$query_string; }
    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:${PHP_SOCK};
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }
    location ~ /\.(?!well-known).* { deny all; }
    client_max_body_size 32M;
}
NGINX
fi

nginx -t && systemctl reload nginx

cd "$APP_DIR"
php artisan view:clear
php artisan cache:clear
timeout 120 php -d memory_limit=512M artisan storefront:warm-cache --no-interaction || true
php artisan config:cache
php artisan view:cache

curl -skI "https://127.0.0.1/" -H "Host: ${DOMAIN}" | head -4
curl -s "http://127.0.0.1/" -H "Host: ${DOMAIN}" | grep -oE 'Featured Products|Shop by Category|Demo Product' | head -6
echo "NGINX_SSL_OK"
