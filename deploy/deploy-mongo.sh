#!/usr/bin/env bash
set -euo pipefail

DOMAIN="${DOMAIN:-tonu-fashion-cms.tonusoft.com}"
APP_DIR="${APP_DIR:-/var/www/tonu-fashion-cms}"
REPO_URL="${REPO_URL:-https://github.com/tonutonmoy/Tonu-Fashion-CMS.git}"
CERT_EMAIL="${CERT_EMAIL:-admin@gmail.com}"

export DEBIAN_FRONTEND=noninteractive

PHP_VER="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>/dev/null || echo 8.4)"

apt-get update -qq
apt-get install -y -qq curl git unzip nginx certbot python3-certbot-nginx \
  "php${PHP_VER}-fpm" "php${PHP_VER}-cli" "php${PHP_VER}-mbstring" \
  "php${PHP_VER}-xml" "php${PHP_VER}-curl" "php${PHP_VER}-zip" \
  "php${PHP_VER}-gd" "php${PHP_VER}-bcmath" "php${PHP_VER}-intl" \
  "php${PHP_VER}-readline" "php${PHP_VER}-mongodb" 2>/dev/null || true

if ! php -m | grep -qi mongodb; then
  add-apt-repository -y ppa:ondrej/php 2>/dev/null || true
  apt-get update -qq
  apt-get install -y -qq "php${PHP_VER}-mongodb"
fi

if ! command -v composer >/dev/null 2>&1; then
  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

if ! command -v node >/dev/null 2>&1 || ! node -v | grep -qE '^v20'; then
  curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
  apt-get install -y -qq nodejs
fi

systemctl enable nginx "php${PHP_VER}-fpm" 2>/dev/null || true
systemctl start nginx "php${PHP_VER}-fpm" 2>/dev/null || true

if [ -d "${APP_DIR}/.git" ]; then
  git -C "${APP_DIR}" fetch origin main
  git -C "${APP_DIR}" reset --hard origin/main
else
  rm -rf "${APP_DIR}"
  git clone "${REPO_URL}" "${APP_DIR}"
fi

cd "${APP_DIR}"

if [ ! -f .env ]; then
  cp .env.example .env
fi

php -r "
\$env = file_get_contents('.env');
\$repl = [
  'APP_ENV=local' => 'APP_ENV=production',
  'APP_DEBUG=true' => 'APP_DEBUG=false',
  'APP_URL=http://127.0.0.1:8000' => 'APP_URL=https://${DOMAIN}',
  'APP_URL=http://localhost' => 'APP_URL=https://${DOMAIN}',
  'LICENSE_SKIP_LOCAL=true' => 'LICENSE_SKIP_LOCAL=false',
];
echo str_replace(array_keys(\$repl), array_values(\$repl), \$env);
" > .env.tmp && mv .env.tmp .env

export COMPOSER_ALLOW_SUPERUSER=1

composer install --no-dev --optimize-autoloader --no-interaction || \
  composer update mongodb/mongodb mongodb/laravel-mongodb --no-dev --with-all-dependencies --no-interaction
npm ci --no-audit --no-fund
npm run build

php artisan key:generate --force 2>/dev/null || true
rm -f bootstrap/cache/*.php
php artisan package:discover --ansi
php artisan mongo:install --fresh --no-interaction
php artisan storefront:warm-cache --no-interaction
php artisan storage:link || true
php artisan config:cache
php artisan route:cache
php artisan view:cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
chown www-data:www-data .env
chmod 640 .env

PHP_FPM_SOCK="$(find /var/run/php -maxdepth 1 -name 'php*-fpm.sock' 2>/dev/null | head -1)"
if [ -z "${PHP_FPM_SOCK}" ]; then
  PHP_FPM_SOCK="/var/run/php/php${PHP_VER}-fpm.sock"
fi

cat > "/etc/nginx/sites-available/${DOMAIN}" <<NGINX
server {
    listen 80;
    listen [::]:80;
    server_name ${DOMAIN};
    root ${APP_DIR}/public;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header X-Content-Type-Options "nosniff";

    index index.php;
    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:${PHP_FPM_SOCK};
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 32M;
}
NGINX

ln -sf "/etc/nginx/sites-available/${DOMAIN}" "/etc/nginx/sites-enabled/${DOMAIN}"
rm -f /etc/nginx/sites-enabled/default
nginx -t
systemctl reload nginx
systemctl reload "php${PHP_VER}-fpm" 2>/dev/null || true

if ! certbot certificates 2>/dev/null | grep -q "${DOMAIN}"; then
  certbot --nginx -d "${DOMAIN}" --non-interactive --agree-tos -m "${CERT_EMAIL}" --redirect || true
fi

echo "MONGO_DEPLOY_OK https://${DOMAIN}"
