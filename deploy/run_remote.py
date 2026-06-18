#!/usr/bin/env python3
"""Upload and run server-setup.sh on VPS. Password via VPS_ROOT_PASS env."""
import os
import secrets
import sys
from pathlib import Path

import paramiko

HOST = "139.180.141.121"
USER = "root"
SCRIPT = Path(__file__).resolve().parent / "server-setup.sh"


def main() -> int:
    password = os.environ.get("VPS_ROOT_PASS")
    if not password:
        print("Set VPS_ROOT_PASS environment variable.", file=sys.stderr)
        return 1

    db_pass = secrets.token_hex(16)
    script = SCRIPT.read_text(encoding="utf-8")

    client = paramiko.SSHClient()
    client.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    print(f"Connecting to {HOST}...")
    client.connect(HOST, username=USER, password=password, timeout=60)

    sftp = client.open_sftp()
    try:
        with sftp.file("/root/deploy.env", "r") as remote:
            for line in remote:
                if line.startswith("DB_PASS="):
                    db_pass = line.strip().split("=", 1)[1]
                    break
    except OSError:
        pass

    with sftp.file("/root/server-setup.sh", "w") as remote:
        remote.write(script)
    sftp.chmod("/root/server-setup.sh", 0o755)
    with sftp.file("/root/deploy.env", "w") as remote:
        remote.write(f"DB_PASS={db_pass}\n")
    sftp.close()

    print("Running server setup (may take 10+ minutes)...")
    stdin, stdout, stderr = client.exec_command(
        "set -a && source /root/deploy.env && set +a && bash /root/server-setup.sh",
        get_pty=True,
    )

    while True:
        line = stdout.readline()
        if not line:
            break
        print(line, end="")

    exit_code = stdout.channel.recv_exit_status()
    err = stderr.read().decode(errors="replace")
    if err.strip():
        print("STDERR:", err, file=sys.stderr)

    client.close()

    print("\n" + "=" * 50)
    print("MySQL database: tonu_fashion_cms")
    print("MySQL user:     tonu_fashion")
    print(f"MySQL password: {db_pass}")
    print("Site URL:       https://tonu-fashion-cms.tonusoft.com")
    print("After deploy:   visit /install to finish setup")
    print(f"Exit code:      {exit_code}")
    print("=" * 50)

    return exit_code


if __name__ == "__main__":
    raise SystemExit(main())
