#!/usr/bin/env bash
ss -tlnp | grep nginx || true
echo "---"
head -50 /etc/nginx/sites-enabled/tonu-fashion-cms.tonusoft.com
echo "---"
certbot certificates 2>/dev/null || true
echo "---"
curl -sI http://127.0.0.1/ -H "Host: tonu-fashion-cms.tonusoft.com" | head -5
