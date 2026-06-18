#!/usr/bin/env bash
DOMAIN="tonu-fashion-cms.tonusoft.com"
echo "=== Page speed (localhost, cache header) ==="
for spec in "/:Home" "/shop:Shop" "/products/demo-product-1-1:Product"; do
  path="${spec%%:*}"
  label="${spec##*:}"
  curl -sk -o /dev/null -w "${label}: %{time_total}s code=%{http_code} cache=%{header_x-storefront-cache}\n" --max-time 30 \
    "https://127.0.0.1${path}" -H "Host: ${DOMAIN}"
done
echo "=== Repeat (should be HIT) ==="
for spec in "/:Home" "/shop:Shop" "/products/demo-product-1-1:Product"; do
  path="${spec%%:*}"
  label="${spec##*:}"
  curl -sk -o /dev/null -w "${label}: %{time_total}s cache=%{header_x-storefront-cache}\n" --max-time 30 \
    "https://127.0.0.1${path}" -H "Host: ${DOMAIN}"
done
