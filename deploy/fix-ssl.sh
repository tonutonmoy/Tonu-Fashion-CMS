#!/usr/bin/env bash
set -euo pipefail

DOMAIN="tonu-fashion-cms.tonusoft.com"
APP_DIR="/var/www/tonu-fashion-cms"

certbot --nginx -d "${DOMAIN}" --non-interactive --agree-tos -m admin@gmail.com --redirect --reinstall || \
  certbot install --cert-name "${DOMAIN}" --nginx

nginx -t
systemctl reload nginx

echo "HTTP:"
curl -sI "http://127.0.0.1/" -H "Host: ${DOMAIN}" | head -3
echo "HTTPS:"
curl -skI "https://127.0.0.1/" -H "Host: ${DOMAIN}" | head -5
BODY=$(curl -sk "https://127.0.0.1/" -H "Host: ${DOMAIN}" | head -c 80)
echo "BODY: ${BODY}"
echo "SSL_FIX_OK https://${DOMAIN}"
