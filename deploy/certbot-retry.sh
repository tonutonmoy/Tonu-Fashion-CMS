#!/usr/bin/env bash
set -euo pipefail
DOMAIN="tonu-fashion-cms.tonusoft.com"

ufw allow 80/tcp 2>/dev/null || true
ufw allow 443/tcp 2>/dev/null || true

for i in 1 2 3; do
  if certbot --nginx -d "${DOMAIN}" --non-interactive --agree-tos -m admin@gmail.com --redirect; then
    echo "CERT_OK"
    break
  fi
  echo "Certbot attempt $i failed, retrying in 15s..."
  sleep 15
done

nginx -t
systemctl reload nginx
ss -tlnp | grep -E ':80|:443' || true
curl -skI "https://127.0.0.1/" -H "Host: ${DOMAIN}" | head -5 || true
