#!/usr/bin/env bash
set -euo pipefail
APP_DIR="/var/www/tonu-fashion-cms"
cd "${APP_DIR}"
git fetch origin main
git reset --hard origin/main
chown www-data:www-data .env
chmod 640 .env
sudo -u www-data php artisan config:cache
sudo -u www-data php artisan route:cache
sudo -u www-data php artisan view:cache
echo "SYNC_OK"
