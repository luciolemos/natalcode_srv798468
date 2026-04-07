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

if ! grep -qi '<title>.*</title>' "$TMP_FILE"; then
  echo "[FAIL] Page title not found in HTML response"
  exit 1
fi

if ! grep -qi 'name="description"' "$TMP_FILE"; then
  echo "[FAIL] Meta description not found in HTML response"
  exit 1
fi

EXPECTED_SNIPPET="${SMOKE_EXPECTED_SNIPPET:-}"
if [[ -n "$EXPECTED_SNIPPET" ]] && ! grep -q "$EXPECTED_SNIPPET" "$TMP_FILE"; then
  echo "[FAIL] Expected snippet not found: $EXPECTED_SNIPPET"
  exit 1
fi

echo "[OK] Smoke check passed for $URL"
