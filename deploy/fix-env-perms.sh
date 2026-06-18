#!/usr/bin/env bash
set -euo pipefail
APP_DIR="/var/www/tonu-fashion-cms"
chown www-data:www-data "${APP_DIR}/.env"
chmod 640 "${APP_DIR}/.env"
sudo -u www-data php -r "echo is_writable('${APP_DIR}/.env') ? 'writable' : 'not-writable';"
echo " done"
