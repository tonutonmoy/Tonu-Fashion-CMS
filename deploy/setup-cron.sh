#!/usr/bin/env bash
set -euo pipefail
APP="/var/www/tonu-fashion-cms"
CRON_LINE="* * * * * cd ${APP} && php artisan schedule:run >> /dev/null 2>&1"

if crontab -l 2>/dev/null | grep -Fq "artisan schedule:run"; then
  echo "Laravel scheduler cron already installed"
else
  (crontab -l 2>/dev/null; echo "$CRON_LINE") | crontab -
  echo "Installed Laravel scheduler cron"
fi

echo "CRON_OK"
