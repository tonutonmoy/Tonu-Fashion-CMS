#!/usr/bin/env bash
set -euo pipefail
export DEBIAN_FRONTEND=noninteractive

systemctl stop mysql mariadb 2>/dev/null || true
rm -f /etc/mysql/FROZEN
rm -rf /var/lib/mysql/*
mkdir -p /var/lib/mysql-files
chown -R mysql:mysql /var/lib/mysql /var/lib/mysql-files

mysqld --initialize-insecure --user=mysql --datadir=/var/lib/mysql
systemctl start mysql
sleep 2
mysql -e "ALTER USER 'root'@'localhost' IDENTIFIED WITH mysql_native_password BY '';"
mysql -e "FLUSH PRIVILEGES;"
systemctl status mysql --no-pager | head -5
mysql -e "SHOW DATABASES;"
