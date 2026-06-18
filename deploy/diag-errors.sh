#!/usr/bin/env bash
APP="/var/www/tonu-fashion-cms"
cd "$APP"
grep -A2 "production.ERROR" storage/logs/laravel.log | tail -30
echo "---"
grep "local.ERROR\|production.ERROR" storage/logs/laravel.log | tail -5
