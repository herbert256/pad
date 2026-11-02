#!/usr/bin/env bash
# Independent fixtures are outside apps/: PAD must never execute these as pages.
set -u
root=$(cd "$(dirname "$0")/../.." && pwd)
cd "$root" || exit 1
log=$(mktemp "${TMPDIR:-/tmp}/pad-subsystem-check.XXXXXX")
trap 'rm -f "$log"' EXIT
passed=0
failed=0
skipped=0
for file in tests/subsystems/*.php tests/subsystems/*.js tests/subsystems/*.cjs; do
  case "${file##*/}" in _*|*-example.php|ui-browser.js|ui-browser.cjs) continue ;; esac
  case "$file" in
    *.php) runner=php ;;
    *) runner=node ;;
  esac
  if ! command -v "$runner" > /dev/null 2>&1; then
    echo "$file: skipped ($runner is not installed)"
    skipped=$((skipped+1))
  elif "$runner" "$file" > "$log" 2>&1; then
    passed=$((passed+1))
  else
    echo "$file: FAILED"
    cat "$log"
    failed=$((failed+1))
  fi
done
printf '%s independent subsystem fixtures passed, %s failed, %s skipped\n' "$passed" "$failed" "$skipped"
[ "$failed" -eq 0 ]
