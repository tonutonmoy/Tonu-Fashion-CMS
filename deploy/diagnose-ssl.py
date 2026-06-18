import os
import paramiko

c = paramiko.SSHClient()
c.set_missing_host_key_policy(paramiko.AutoAddPolicy())
c.connect("139.180.141.121", username="root", password=os.environ["VPS_ROOT_PASS"], timeout=60)

cmds = [
    "grep -l tonu-fashion-cms /etc/nginx/sites-enabled/* 2>/dev/null",
    "grep -n 'listen.*443\\|server_name' /etc/nginx/sites-enabled/tonu-fashion-cms.tonusoft.com 2>/dev/null",
    "certbot certificates 2>/dev/null | grep -A3 tonu-fashion-cms || true",
    "curl -sI http://127.0.0.1/ -H 'Host: tonu-fashion-cms.tonusoft.com' | head -3",
    "curl -skI https://127.0.0.1/ -H 'Host: tonu-fashion-cms.tonusoft.com' | head -5",
    "ss -tlnp | grep ':443'",
]
for cmd in cmds:
    print(">>>", cmd)
    _, out, _ = c.exec_command(cmd)
    print(out.read().decode()[:800])
    print()
c.close()
