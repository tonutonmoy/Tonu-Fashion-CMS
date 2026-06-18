#!/usr/bin/env python3
import json
import re
import time
import http.cookiejar
import urllib.request
import urllib.error

BASE = "https://tonu-fashion-cms.tonusoft.com"
jar = http.cookiejar.CookieJar()
opener = urllib.request.build_opener(urllib.request.HTTPCookieProcessor(jar))


def go(method, path, data=None, extra=None):
    h = {"User-Agent": "QA", "Accept": "application/json", "X-Requested-With": "XMLHttpRequest"}
    if extra:
        h.update(extra)
    body = data.encode() if isinstance(data, str) else data
    r = urllib.request.Request(BASE + path, data=body, headers=h, method=method)
    t0 = time.perf_counter()
    try:
        resp = opener.open(r, timeout=30)
        b = resp.read().decode()
        ms = (time.perf_counter() - t0) * 1000
        return resp.status, b, ms
    except urllib.error.HTTPError as e:
        ms = (time.perf_counter() - t0) * 1000
        return e.code, e.read().decode(), ms


code, home, ms = go("GET", "/")
csrf = re.search(r'name="csrf-token" content="([^"]+)"', home)
token = csrf.group(1) if csrf else ""
print(f"Home: {code} {ms:.0f}ms | CSRF ok={bool(token)} | cookies={len(list(jar))}")

tests = [
    (
        "Support session",
        "/api/support/session",
        {"guest_name": "QA Tester", "guest_phone": "01700000000"},
    ),
    (
        "Shipping quote",
        "/api/bd/shipping-quote",
        {"subtotal": 1000, "division": "Dhaka", "district": "Dhaka"},
    ),
    ("Cart add", "/api/cart", {"product_id": 1, "quantity": 1}),
]
for name, path, payload in tests:
    code, body, ms = go(
        "POST",
        path,
        json.dumps(payload),
        {
            "Content-Type": "application/json",
            "X-CSRF-TOKEN": token,
            "Referer": BASE + "/",
        },
    )
    ok = code in (200, 201, 422)
    tag = "OK" if ok else "!!"
    print(f"[{tag}] {name}: {code} {ms:.0f}ms | {body[:200]}")

code, body, ms = go("GET", "/up")
print(f"Health /up: {code} {ms:.0f}ms | {body[:120]}")
