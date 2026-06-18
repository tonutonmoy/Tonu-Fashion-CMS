#!/usr/bin/env bash
set -euo pipefail

DOMAIN="${DOMAIN:-tonu-fashion-cms.tonusoft.com}"
APP_DIR="${APP_DIR:-/var/www/tonu-fashion-cms}"
REPO_URL="${REPO_URL:-https://github.com/tonutonmoy/Tonu-Fashion-CMS.git}"
CERT_EMAIL="${CERT_EMAIL:-admin@gmail.com}"

export DEBIAN_FRONTEND=noninteractive
export COMPOSER_ALLOW_SUPERUSER=1
export COMPOSER_MEMORY_LIMIT=512M
export NODE_OPTIONS="--max-old-space-size=512"

PHP_VER="$(php -r 'echo PHP_MAJOR_VERSION.".".PHP_MINOR_VERSION;' 2>/dev/null || echo 8.4)"
PHP_INI="/etc/php/${PHP_VER}/fpm/php.ini"
PHP_POOL="/etc/php/${PHP_VER}/fpm/pool.d/www.conf"

# --- Low RAM: 1GB swap if missing ---
if ! swapon --show | grep -q '/swapfile'; then
  if [ ! -f /swapfile ]; then
    fallocate -l 1G /swapfile 2>/dev/null || dd if=/dev/zero of=/swapfile bs=1M count=1024 status=none
    chmod 600 /swapfile
    mkswap /swapfile
  fi
  swapon /swapfile 2>/dev/null || true
  grep -q '/swapfile' /etc/fstab || echo '/swapfile none swap sw 0 0' >> /etc/fstab
fi

# --- Packages (skip apt if stack already present) ---
if ! php -m 2>/dev/null | grep -qi mongodb || ! command -v composer >/dev/null 2>&1 || ! command -v node >/dev/null 2>&1; then
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
fi

# --- PHP low-memory tuning ---
if [ -f "${PHP_INI}" ]; then
  sed -i 's/^memory_limit = .*/memory_limit = 256M/' "${PHP_INI}" 2>/dev/null || true
  sed -i 's/^;*opcache.enable=.*/opcache.enable=1/' "${PHP_INI}" 2>/dev/null || true
  sed -i 's/^;*opcache.memory_consumption=.*/opcache.memory_consumption=64/' "${PHP_INI}" 2>/dev/null || true
fi
if [ -f "${PHP_POOL}" ]; then
  sed -i 's/^pm = .*/pm = ondemand/' "${PHP_POOL}" 2>/dev/null || true
  sed -i 's/^pm.max_children = .*/pm.max_children = 8/' "${PHP_POOL}" 2>/dev/null || true
  sed -i 's/^;*pm.process_idle_timeout = .*/pm.process_idle_timeout = 10s/' "${PHP_POOL}" 2>/dev/null || true
  sed -i 's/^;*pm.max_requests = .*/pm.max_requests = 200/' "${PHP_POOL}" 2>/dev/null || true
fi

systemctl enable nginx "php${PHP_VER}-fpm" 2>/dev/null || true
systemctl restart "php${PHP_VER}-fpm" 2>/dev/null || true
systemctl start nginx 2>/dev/null || true

# --- Clone / update ---
if [ -d "${APP_DIR}/.git" ]; then
  git -C "${APP_DIR}" fetch origin main
  git -C "${APP_DIR}" reset --hard origin/main
else
  rm -rf "${APP_DIR}"
  git clone --depth 1 "${REPO_URL}" "${APP_DIR}"
fi

cd "${APP_DIR}"
if [ ! -f artisan ]; then
  echo "ERROR: artisan not found in ${APP_DIR}"
  exit 1
fi

run_artisan() {
  (cd "${APP_DIR}" && php -d memory_limit=512M artisan "$@")
}

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
  'CACHE_STORE=database' => 'CACHE_STORE=file',
  'CACHE_STORE=redis' => 'CACHE_STORE=file',
  'QUEUE_CONNECTION=database' => 'QUEUE_CONNECTION=sync',
  'QUEUE_CONNECTION=redis' => 'QUEUE_CONNECTION=sync',
];
if (!str_contains(\$env, 'PERFORMANCE_PROFILING=')) {
  \$env .= \"\\nPERFORMANCE_PROFILING=false\\n\";
} else {
  \$repl['PERFORMANCE_PROFILING=true'] = 'PERFORMANCE_PROFILING=false';
}
if (!str_contains(\$env, 'STOREFRONT_CACHE_TTL=')) {
  \$env .= \"\\nSTOREFRONT_CACHE_TTL=3600\\n\";
}
echo str_replace(array_keys(\$repl), array_values(\$repl), \$env);
" > .env.tmp && mv .env.tmp .env

mkdir -p bootstrap/cache storage/framework/{cache/data,sessions,views} storage/logs
chmod -R 775 bootstrap/cache storage 2>/dev/null || true

(cd "${APP_DIR}" && composer install --no-dev --optimize-autoloader --no-interaction --prefer-dist) || \
  (cd "${APP_DIR}" && composer update mongodb/mongodb mongodb/laravel-mongodb --no-dev --with-all-dependencies --no-interaction)

(cd "${APP_DIR}" && npm ci --no-audit --no-fund --prefer-offline 2>/dev/null || npm install --no-audit --no-fund)
(cd "${APP_DIR}" && npm run build)

run_artisan key:generate --force 2>/dev/null || true
rm -f bootstrap/cache/*.php
run_artisan package:discover --ansi
run_artisan mongo:create-indexes --no-interaction 2>/dev/null || true
run_artisan mongo:install --fresh --no-interaction
run_artisan storefront:warm-cache --no-interaction
run_artisan storage:link 2>/dev/null || true
run_artisan config:cache
run_artisan route:cache
run_artisan view:cache

chown -R www-data:www-data storage bootstrap/cache
chmod -R 775 storage bootstrap/cache
chown www-data:www-data .env
chmod 640 .env

PHP_FPM_SOCK="$(find /var/run/php -maxdepth 1 -name 'php*-fpm.sock' 2>/dev/null | head -1)"
[ -z "${PHP_FPM_SOCK}" ] && PHP_FPM_SOCK="/var/run/php/php${PHP_VER}-fpm.sock"

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
    location = /robots.txt {
        access_log off;
        try_files \$uri /index.php?\$query_string;
    }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:${PHP_FPM_SOCK};
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
        fastcgi_buffers 8 16k;
        fastcgi_buffer_size 32k;
    }

    location ~ /\.(?!well-known).* {
        deny all;
    }

    client_max_body_size 32M;
}
NGINX

ln -sf "/etc/nginx/sites-available/${DOMAIN}" "/etc/nginx/sites-enabled/${DOMAIN}"
rm -f /etc/nginx/sites-enabled/default 2>/dev/null || true
nginx -t
systemctl reload nginx
systemctl reload "php${PHP_VER}-fpm" 2>/dev/null || true

certbot --nginx -d "${DOMAIN}" --non-interactive --agree-tos -m "${CERT_EMAIL}" --redirect --reinstall 2>/dev/null || \
  certbot --nginx -d "${DOMAIN}" --non-interactive --agree-tos -m "${CERT_EMAIL}" --redirect 2>/dev/null || true

nginx -t && systemctl reload nginx

echo "MONGO_DEPLOY_OK https://${DOMAIN}"
free -h
php -d memory_limit=128M -r "echo 'PHP OK';"
