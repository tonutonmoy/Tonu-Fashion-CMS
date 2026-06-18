#!/usr/bin/env bash
echo "=== nginx.conf proxy_cache ==="
grep -n proxy_cache /etc/nginx/nginx.conf || true
echo "=== api site head ==="
head -20 /etc/nginx/sites-available/api.tonusoft.com 2>/dev/null || true
echo "=== fashion site ==="
head -30 /etc/nginx/sites-available/tonu-fashion-cms.tonusoft.com 2>/dev/null || true
echo "=== letsencrypt ==="
ls /etc/letsencrypt/live/ 2>/dev/null || true
