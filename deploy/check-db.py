import os
import paramiko

c = paramiko.SSHClient()
c.set_missing_host_key_policy(paramiko.AutoAddPolicy())
c.connect("139.180.141.121", username="root", password=os.environ["VPS_ROOT_PASS"], timeout=60)
queries = [
    "SELECT section_key, enabled, sort_order FROM homepage_sections ORDER BY sort_order LIMIT 15;",
    "SELECT COUNT(*) AS products FROM products;",
    "SELECT COUNT(*) AS categories FROM categories;",
    "SELECT value FROM settings WHERE `group`='app' AND `key`='installed' LIMIT 1;",
]
for q in queries:
    cmd = (
        "mysql -u tonu_fashion -p76676239168794ed798da19bde0a31f9 tonu_fashion_cms "
        f"-e \"{q}\" 2>/dev/null"
    )
    print(">>>", q)
    _, o, _ = c.exec_command(cmd)
    print(o.read().decode())
c.close()
