#!/usr/bin/env bash
set -euo pipefail
grep -rE 'fastcgi_cache|proxy_cache|microcache' /etc/nginx/sites-enabled/ /etc/nginx/nginx.conf 2>/dev/null | head -30
cat /etc/nginx/sites-enabled/*tonu* 2>/dev/null || cat /etc/nginx/sites-enabled/default 2>/dev/null | head -80
