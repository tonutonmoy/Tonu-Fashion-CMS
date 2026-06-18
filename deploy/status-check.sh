#!/usr/bin/env bash
echo "=== SERVICES ==="
systemctl is-active nginx php8.4-fpm 2>/dev/null
echo "=== PORTS ==="
ss -tlnp | grep -E ':80|:443' | head -6
echo "=== PROCESSES ==="
pgrep -af "artisan|php-fpm" | head -15
echo "=== RAM ==="
free -h | head -2
echo "=== CURL HTTP ==="
curl -sI --max-time 15 http://127.0.0.1/ -H "Host: tonu-fashion-cms.tonusoft.com" | head -5
echo "=== CURL HTTPS ==="
curl -skI --max-time 30 https://127.0.0.1/ -H "Host: tonu-fashion-cms.tonusoft.com" | head -5
echo "=== PHP TEST ==="
cd /var/www/tonu-fashion-cms && timeout 30 php -r "echo 'php_ok';" 2>&1
echo "=== MONGO ==="
cd /var/www/tonu-fashion-cms && timeout 30 php artisan tinker --execute="echo App\Models\Product::count();" 2>&1 | tail -3
echo "=== NGINX ERROR (last 5) ==="
tail -5 /var/log/nginx/error.log 2>/dev/null
echo "DIAG_DONE"
