#!/usr/bin/env bash
grep "production.ERROR" /var/www/tonu-fashion-cms/storage/logs/laravel.log | tail -3
echo "---"
curl -sk "https://127.0.0.1/shop" -H "Host: tonu-fashion-cms.tonusoft.com" | head -c 800
