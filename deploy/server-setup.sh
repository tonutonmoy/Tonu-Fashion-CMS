#!/usr/bin/env bash
set -euo pipefail

DOMAIN="${DOMAIN:-tonu-fashion-cms.tonusoft.com}"
APP_DIR="${APP_DIR:-/var/www/tonu-fashion-cms}"
DB_NAME="${DB_NAME:-tonu_fashion_cms}"
DB_USER="${DB_USER:-tonu_fashion}"
DB_PASS="${DB_PASS:?DB_PASS required}"
REPO_URL="${REPO_URL:-https://github.com/tonutonmoy/Tonu-Fashion-CMS.git}"
CERT_EMAIL="${CERT_EMAIL:-admin@gmail.com}"

export DEBIAN_FRONTEND=noninteractive

apt-get update -qq
apt-get install -y -qq software-properties-common curl git unzip nginx certbot python3-certbot-nginx

# Fix MySQL frozen state (MariaDB -> MySQL downgrade) on fresh deploys.
if [ -e /etc/mysql/FROZEN ]; then
  systemctl stop mysql mariadb 2>/dev/null || true
  rm -rf /var/lib/mysql/*
  rm -f /etc/mysql/FROZEN
fi

mkdir -p /var/lib/mysql-files
chown mysql:mysql /var/lib/mysql-files

if ! dpkg -l | grep -qE '^ii\s+mysql-server'; then
  apt-get install -y -qq mysql-server
fi

systemctl enable mysql 2>/dev/null || true
systemctl start mysql 2>/dev/null || true

PHP_VER="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>/dev/null || true)"
if [ -z "${PHP_VER}" ]; then
  add-apt-repository -y ppa:ondrej/php
  apt-get update -qq
  PHP_VER="8.3"
fi

PHP_PACKAGES=(
  "php${PHP_VER}-fpm"
  "php${PHP_VER}-cli"
  "php${PHP_VER}-mysql"
  "php${PHP_VER}-mbstring"
  "php${PHP_VER}-xml"
  "php${PHP_VER}-curl"
  "php${PHP_VER}-zip"
  "php${PHP_VER}-gd"
  "php${PHP_VER}-bcmath"
  "php${PHP_VER}-intl"
  "php${PHP_VER}-readline"
)
apt-get install -y -qq "${PHP_PACKAGES[@]}"

if ! command -v composer >/dev/null 2>&1; then
  curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
fi

if ! command -v node >/dev/null 2>&1 || ! node -v | grep -qE '^v20'; then
  curl -fsSL https://deb.nodesource.com/setup_20.x | bash -
  apt-get install -y -qq nodejs
fi

systemctl enable nginx 2>/dev/null || true
systemctl start nginx 2>/dev/null || true
systemctl enable "php${PHP_VER}-fpm" 2>/dev/null || true
systemctl start "php${PHP_VER}-fpm" 2>/dev/null || true

mysql -e "CREATE DATABASE IF NOT EXISTS \`${DB_NAME}\` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "ALTER USER '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}';"
mysql -e "GRANT ALL PRIVILEGES ON \`${DB_NAME}\`.* TO '${DB_USER}'@'localhost';"
mysql -e "FLUSH PRIVILEGES;"

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
  'APP_URL=http://localhost' => 'APP_URL=https://${DOMAIN}',
  'DB_DATABASE=fashion_store' => 'DB_DATABASE=${DB_NAME}',
  'DB_USERNAME=root' => 'DB_USERNAME=${DB_USER}',
  'DB_PASSWORD=' => 'DB_PASSWORD=${DB_PASS}',
  'LICENSE_SKIP_LOCAL=true' => 'LICENSE_SKIP_LOCAL=false',
];
echo str_replace(array_keys(\$repl), array_values(\$repl), \$env);
" > .env.tmp && mv .env.tmp .env

composer install --no-dev --optimize-autoloader --no-interaction
npm ci --no-audit --no-fund
npm run build
php artisan key:generate --force
php artisan migrate --force
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

if ! certbot certificates 2>/dev/null | grep -q "${DOMAIN}"; then
  certbot --nginx -d "${DOMAIN}" --non-interactive --agree-tos -m "${CERT_EMAIL}" --redirect || true
fi

echo "DEPLOY_OK ${DOMAIN}"
