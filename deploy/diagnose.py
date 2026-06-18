import os
import paramiko

password = os.environ["VPS_ROOT_PASS"]
c = paramiko.SSHClient()
c.set_missing_host_key_policy(paramiko.AutoAddPolicy())
c.connect("139.180.141.121", username="root", password=password, timeout=60)

cmds = [
    "cat /etc/mysql/FROZEN 2>/dev/null || echo no-frozen",
    "systemctl is-active mysql || true; systemctl is-active mariadb || true",
    "dpkg -l | grep -E 'mysql|mariadb|php' | head -25",
    "php -v 2>&1 | head -1 || true",
    "ls -la /var/www/tonu-fashion-cms 2>/dev/null | head -5 || echo no-app",
]
for cmd in cmds:
    print(">>>", cmd)
    _, out, err = c.exec_command(cmd)
    print(out.read().decode())
    e = err.read().decode()
    if e.strip():
        print("ERR", e)
c.close()
