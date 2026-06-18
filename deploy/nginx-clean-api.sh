#!/usr/bin/env bash
API="/etc/nginx/sites-available/api.tonusoft.com"
# Strip directives that belong in http{} block only
grep -vE '^(proxy_cache_path|gzip |gzip_)' "$API" > /tmp/api.clean && mv /tmp/api.clean "$API"
nginx -t && echo NGINX_TEST_OK
