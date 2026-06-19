#!/usr/bin/env python3
"""QA: performance + functional + API tests against live site."""
import json
import re
import time
import urllib.error
import urllib.request
from dataclasses import dataclass, field

BASE = "https://tonu-fashion-cms.tonusoft.com"


@dataclass
class Result:
    name: str
    status: str  # PASS, FAIL, WARN
    detail: str
    ms: float = 0


results: list[Result] = []


def req(
    method: str,
    path: str,
    *,
    headers: dict | None = None,
    data: bytes | None = None,
    accept: str = "text/html",
) -> tuple[int, str, float, dict]:
    url = BASE + path
    h = {
        "User-Agent": "FashionBD-QA/1.0",
        "Accept": accept,
    }
    if headers:
        h.update(headers)
    r = urllib.request.Request(url, data=data, headers=h, method=method)
    t0 = time.perf_counter()
    try:
        with urllib.request.urlopen(r, timeout=30) as resp:
            body = resp.read().decode("utf-8", errors="replace")
            elapsed = (time.perf_counter() - t0) * 1000
            return resp.status, body, elapsed, dict(resp.headers)
    except urllib.error.HTTPError as e:
        elapsed = (time.perf_counter() - t0) * 1000
        body = e.read().decode("utf-8", errors="replace")
        return e.code, body, elapsed, dict(e.headers)


def perf(name: str, path: str, max_ms: float = 3000):
    code, body, ms, _ = req("GET", path)
    ok = code == 200 and ms <= max_ms
    results.append(
        Result(
            name=f"Perf: {name}",
            status="PASS" if ok else ("WARN" if code == 200 else "FAIL"),
            detail=f"{code} in {ms:.0f}ms (limit {max_ms:.0f}ms)",
            ms=ms,
        )
    )


def page(name: str, path: str, must_contain: list[str], must_not: list[str] | None = None):
    code, body, ms, _ = req("GET", path)
    missing = [s for s in must_contain if s not in body]
    bad = [s for s in (must_not or []) if s in body]
    ok = code == 200 and not missing and not bad
    detail = f"{code} {ms:.0f}ms"
    if missing:
        detail += f" | missing: {missing}"
    if bad:
        detail += f" | unwanted: {bad}"
    results.append(Result(f"Page: {name}", "PASS" if ok else "FAIL", detail, ms))


def api_json(name: str, path: str, method: str = "GET", expect_keys: list[str] | None = None):
    code, body, ms, _ = req(
        "GET" if method == "GET" else method,
        path,
        accept="application/json",
        headers={"Accept": "application/json", "X-Requested-With": "XMLHttpRequest"},
    )
    parsed = None
    err = None
    try:
        parsed = json.loads(body) if body.strip() else {}
    except json.JSONDecodeError as e:
        err = str(e)
    keys_ok = True
    if expect_keys and parsed is not None:
        keys_ok = all(k in parsed for k in expect_keys)
    ok = code == 200 and err is None and keys_ok
    detail = f"{code} {ms:.0f}ms"
    if err:
        detail += f" | JSON error: {err[:60]}"
    elif expect_keys and not keys_ok:
        detail += f" | keys missing in {list(parsed.keys())[:8]}"
    results.append(Result(f"API: {name}", "PASS" if ok else "FAIL", detail, ms))


# --- Performance (cold + warm) ---
for label, path in [
    ("Home", "/"),
    ("Shop", "/shop"),
    ("Product", "/products/demo-product-1-1"),
    ("Cart", "/cart"),
    ("Admin login", "/admin/login"),
]:
    perf(label, path, max_ms=5000)
    perf(f"{label} (2nd)", path, max_ms=3000)

# --- Functional pages ---
page("Home", "/", ["Fashion BD", "theme-product"], ["imgbb.com image not found", "i.ibb.co"])
page("Shop", "/shop", ["shop-product-grid", "Demo Product"], ["imgbb.com"])
page("Product detail", "/products/demo-product-1-1", ["theme-product", "Demo Product 1"], ["BadMethodCallException"])
page("Blog", "/blog", ["blog", "Fashion"], [])
page("Cart page", "/cart", ["cart", "Cart"], [])
page("Sitemap", "/sitemap.xml", ["<urlset", "<loc>"], [])
page("Robots", "/robots.txt", ["User-agent", "Sitemap"], [])
page("Admin login", "/admin/login", ["Admin", "login", "password"], [])

# --- API endpoints ---
api_json("Cart API", "/api/cart", expect_keys=["items", "count", "subtotal"])
api_json("BD Divisions", "/api/bd/divisions", expect_keys=[])
code, body, ms, _ = req("GET", "/api/bd/divisions", accept="application/json")
is_list = code == 200 and body.strip().startswith("[")
results.append(
    Result(
        "API: BD Divisions list",
        "PASS" if is_list and len(body) > 10 else "FAIL",
        f"{code} {ms:.0f}ms len={len(body)}",
        ms,
    )
)

code, body, ms, _ = req(
    "GET",
    "/shop?ajax=1",
    accept="application/json",
    headers={"X-Requested-With": "XMLHttpRequest", "Accept": "application/json"},
)
shop_ajax = code == 200 and '"html"' in body
results.append(
    Result(
        "API: Shop AJAX filter",
        "PASS" if shop_ajax else "FAIL",
        f"{code} {ms:.0f}ms has html={shop_ajax}",
        ms,
    )
)

code, body, ms, _ = req("GET", "/home/section/new_arrivals", accept="application/json")
lazy_ok = code == 200 and '"html"' in body
results.append(
    Result(
        "API: Home lazy section",
        "PASS" if lazy_ok else "FAIL",
        f"{code} {ms:.0f}ms",
        ms,
    )
)

# Support chat session (POST)
csrf_code, csrf_body, _, _ = req("GET", "/")
csrf = re.search(r'name="csrf-token" content="([^"]+)"', csrf_body)
csrf_token = csrf.group(1) if csrf else ""
post_data = json.dumps({"guest_name": "QA Tester", "guest_phone": "01700000000"}).encode()
code, body, ms, _ = req(
    "POST",
    "/api/support/session",
    data=post_data,
    headers={
        "Content-Type": "application/json",
        "Accept": "application/json",
        "X-CSRF-TOKEN": csrf_token,
        "X-Requested-With": "XMLHttpRequest",
        "Referer": BASE + "/",
    },
)
support_ok = code in (200, 201) and "conversation" in body.lower() or "id" in body
results.append(
    Result(
        "API: Support chat session",
        "PASS" if support_ok else "WARN",
        f"{code} {ms:.0f}ms",
        ms,
    )
)

# 404 handling
code, _, ms, _ = req("GET", "/products/nonexistent-slug-xyz")
results.append(
    Result("Func: 404 product", "PASS" if code == 404 else "FAIL", f"HTTP {code} {ms:.0f}ms", ms)
)

# Summary
passed = sum(1 for r in results if r.status == "PASS")
warned = sum(1 for r in results if r.status == "WARN")
failed = sum(1 for r in results if r.status == "FAIL")
perfs = [r for r in results if r.name.startswith("Perf:") and "(2nd)" in r.name]
avg_warm = sum(r.ms for r in perfs) / len(perfs) if perfs else 0

print("=" * 60)
print(f"QA REPORT — {BASE}")
print("=" * 60)
for r in results:
    icon = {"PASS": "OK", "WARN": "!!", "FAIL": "XX"}[r.status]
    print(f"[{icon}] {r.name}: {r.detail}")
print("-" * 60)
print(f"PASS: {passed} | WARN: {warned} | FAIL: {failed}")
print(f"Avg warm page load: {avg_warm:.0f}ms")
print("=" * 60)
exit(1 if failed else 0)
