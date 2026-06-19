#!/usr/bin/env bash
set -euo pipefail
APP="/var/www/tonu-fashion-cms"
cd "$APP"
grep -E 'STOREFRONT_HTML_CACHE|CACHE_DRIVER|APP_URL' .env || true
sudo -u www-data php -r "require 'vendor/autoload.php'; \$app=require 'bootstrap/app.php'; \$k=\$app->make('Illuminate\Contracts\Http\Kernel'); \$r=Illuminate\Http\Request::create('/', 'GET'); \$r->headers->set('Host','tonu-fashion-cms.tonusoft.com'); \$r->server->set('HTTP_HOST','tonu-fashion-cms.tonusoft.com'); \$t=microtime(true); \$res=\$k->handle(\$r); echo 'php_kernel cache='.\$res->headers->get('X-Storefront-Cache','none').' time='.round(microtime(true)-\$t,3).'s'.PHP_EOL; \$k->terminate(\$r,\$res);"
curl -sk -o /dev/null -w "nginx_time=%{time_total}s\n" "https://127.0.0.1/" -H "Host: tonu-fashion-cms.tonusoft.com"
curl -sk -I "https://127.0.0.1/" -H "Host: tonu-fashion-cms.tonusoft.com" | tr -d '\r' | grep -i x-storefront || echo "nginx: no X-Storefront-Cache header"
grep -o 'html_cache[^,]*' bootstrap/cache/config.php 2>/dev/null | head -1 || echo "no config cache"
