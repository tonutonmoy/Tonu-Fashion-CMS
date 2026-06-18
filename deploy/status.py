import os
import paramiko

password = os.environ["VPS_ROOT_PASS"]
c = paramiko.SSHClient()
c.set_missing_host_key_policy(paramiko.AutoAddPolicy())
c.connect("139.180.141.121", username="root", password=password, timeout=60)

cmds = [
    "tail -40 /var/log/mysql/error.log 2>/dev/null || journalctl -u mysql -n 20 --no-pager -o cat",
    "cat /etc/mysql/mysql.conf.d/mysqld.cnf | grep -E 'datadir|socket|bind' || true",
]
for cmd in cmds:
    print(">>>", cmd)
    _, out, err = c.exec_command(cmd, timeout=30)
    text = out.read().decode(errors="replace")
    print(text)
    e = err.read().decode()
    if e.strip():
        print("ERR", e)
c.close()
