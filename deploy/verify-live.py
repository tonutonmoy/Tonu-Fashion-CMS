import os
import paramiko

c = paramiko.SSHClient()
c.set_missing_host_key_policy(paramiko.AutoAddPolicy())
c.connect("139.180.141.121", username="root", password=os.environ["VPS_ROOT_PASS"], timeout=60)

cmds = [
    "test -f /var/www/tonu-fashion-cms/artisan && echo APP_OK",
    'curl -sI -H "Host: tonu-fashion-cms.tonusoft.com" http://127.0.0.1/ | head -5',
    'curl -sL -H "Host: tonu-fashion-cms.tonusoft.com" http://127.0.0.1/ | head -c 400',
    "nslookup tonu-fashion-cms.tonusoft.com 2>/dev/null | tail -3",
]
for cmd in cmds:
    print(">>>", cmd)
    _, out, _ = c.exec_command(cmd)
    print(out.read().decode())
c.close()
