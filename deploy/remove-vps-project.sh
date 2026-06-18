#!/usr/bin/env bash
set -euo pipefail

DOMAIN="${DOMAIN:-tonu-fashion-cms.tonusoft.com}"
APP_DIR="${APP_DIR:-/var/www/tonu-fashion-cms}"

echo "Removing nginx site for ${DOMAIN}..."
rm -f "/etc/nginx/sites-enabled/${DOMAIN}"
rm -f "/etc/nginx/sites-available/${DOMAIN}"

if [ -d "${APP_DIR}" ]; then
  echo "Removing app directory ${APP_DIR}..."
  rm -rf "${APP_DIR}"
fi

rm -f /root/server-setup.sh /root/deploy-mongo.sh /root/deploy.env 2>/dev/null || true

nginx -t
systemctl reload nginx

echo "VPS_PROJECT_REMOVED ${DOMAIN}"
