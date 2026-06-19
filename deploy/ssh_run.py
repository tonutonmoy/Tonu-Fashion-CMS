import os
import sys
from pathlib import Path

import paramiko

HOST = "139.180.141.121"
USER = "root"


def run_remote(script_path: Path, extra_env: str = "") -> int:
    password = os.environ.get("VPS_ROOT_PASS")
    if not password:
        print("Set VPS_ROOT_PASS", file=sys.stderr)
        return 1

    script = script_path.read_text(encoding="utf-8")
    c = paramiko.SSHClient()
    c.set_missing_host_key_policy(paramiko.AutoAddPolicy())
    c.connect(HOST, username=USER, password=password, timeout=60)
    sftp = c.open_sftp()
    remote = f"/root/{script_path.name}"
    with sftp.file(remote, "w") as f:
        f.write(script)
    sftp.chmod(remote, 0o755)
    sftp.close()

    cmd = f"{extra_env} bash {remote}"
    print(f"Running: {script_path.name}")
    _, stdout, stderr = c.exec_command(cmd, get_pty=True, timeout=1800)
    while True:
        line = stdout.readline()
        if not line:
            break
        try:
            print(line, end="")
        except UnicodeEncodeError:
            print(line.encode("ascii", errors="replace").decode("ascii"), end="")
    code = stdout.channel.recv_exit_status()
    err = stderr.read().decode(errors="replace")
    if err.strip():
        print("STDERR:", err)
    c.close()
    return code


if __name__ == "__main__":
    base = Path(__file__).resolve().parent
    name = sys.argv[1] if len(sys.argv) > 1 else "server-setup.sh"
    env = sys.argv[2] if len(sys.argv) > 2 else ""
    if name == "server-setup.sh":
        env = "set -a && source /root/deploy.env && set +a &&"
    raise SystemExit(run_remote(base / name, env))
