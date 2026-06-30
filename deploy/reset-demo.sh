#!/usr/bin/env bash
set -euo pipefail
APP="/var/www/tonu-fashion-cms"
cd "$APP"
sudo -u www-data php artisan fashion:reset-demo --warm --no-interaction
echo "RESET_DEMO_OK"
