#!/usr/bin/env bash
DOMAIN="tonu-fashion-cms.tonusoft.com"
SLUG="demo-product-1-1"
echo "=== Product page bench (localhost) ==="
for i in 1 2 3 4 5; do
  curl -sk -o /dev/null -w "run $i: %{time_total}s code=%{http_code}\n" --max-time 30 \
    "https://127.0.0.1/products/${SLUG}" -H "Host: ${DOMAIN}"
done
echo "=== Recent errors ==="
tail -20 /var/www/tonu-fashion-cms/storage/logs/laravel.log
