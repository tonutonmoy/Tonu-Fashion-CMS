import os
import paramiko

c = paramiko.SSHClient()
c.set_missing_host_key_policy(paramiko.AutoAddPolicy())
c.connect("139.180.141.121", username="root", password=os.environ["VPS_ROOT_PASS"], timeout=60)

cmds = [
    "grep -r 'server is running' /var/www /etc/nginx 2>/dev/null | head -5",
    "cat /etc/nginx/sites-enabled/tonu-fashion-cms.tonusoft.com 2>/dev/null | head -20",
    "ls -la /var/www/tonu-fashion-cms/public/index.php 2>/dev/null",
    "curl -s http://127.0.0.1:80 -H 'Host: tonu-fashion-cms.tonusoft.com' | head -c 120",
]
for cmd in cmds:
    print(">>>", cmd)
    _, out, err = c.exec_command(cmd)
    print(out.read().decode()[:600])
    e = err.read().decode().strip()
    if e:
        print("ERR:", e[:200])
c.close()
