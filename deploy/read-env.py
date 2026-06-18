import os
import paramiko

password = os.environ.get("VPS_ROOT_PASS", "")
if not password:
    raise SystemExit("no pass")
c = paramiko.SSHClient()
c.set_missing_host_key_policy(paramiko.AutoAddPolicy())
c.connect("139.180.141.121", username="root", password=password, timeout=60)
_, out, _ = c.exec_command("cat /var/www/tonu-fashion-cms/.env")
print(out.read().decode())
c.close()
