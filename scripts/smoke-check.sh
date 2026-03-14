#!/usr/bin/env bash
set -euo pipefail

URL="${1:-${SMOKE_URL:-}}"
if [[ -z "$URL" ]]; then
  echo "Usage: $0 <url>"
  echo "or set SMOKE_URL env var"
  exit 2
fi

TMP_FILE="$(mktemp)"
trap 'rm -f "$TMP_FILE"' EXIT

STATUS_CODE="$(curl -sS -o "$TMP_FILE" -w "%{http_code}" "$URL")"

if [[ "$STATUS_CODE" -ge 500 ]]; then
  echo "[FAIL] HTTP status $STATUS_CODE from $URL"
  exit 1
fi

if ! grep -q '</html>' "$TMP_FILE"; then
  echo "[FAIL] HTML appears truncated (missing </html>)"
  tail -n 60 "$TMP_FILE" || true
  exit 1
fi

if grep -q '"statusCode": 500' "$TMP_FILE"; then
  echo "[FAIL] Backend JSON error injected into HTML response"
  tail -n 60 "$TMP_FILE" || true
  exit 1
fi

if ! grep -q 'cedern-nav.js' "$TMP_FILE"; then
  echo "[FAIL] Navigation script reference not found in response"
  exit 1
fi

echo "[OK] Smoke check passed for $URL"
