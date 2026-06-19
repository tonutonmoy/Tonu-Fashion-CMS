#!/usr/bin/env bash
grep -A2 "production.ERROR" /var/www/tonu-fashion-cms/storage/logs/laravel.log | tail -30
