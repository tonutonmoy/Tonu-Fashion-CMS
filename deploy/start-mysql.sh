#!/usr/bin/env bash
set -euo pipefail
mkdir -p /var/lib/mysql-files
chown mysql:mysql /var/lib/mysql-files
systemctl restart mysql
sleep 2
systemctl is-active mysql
mysql -e "SHOW DATABASES;"
