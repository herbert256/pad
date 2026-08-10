#!/usr/bin/env bash

# The suites as a gate: run everything, read what the runs left in DATA/suites/, and exit
# nonzero when anything failed - which is what a git hook or a CI step can act on.
#
# The gate fails closed: the trigger must answer 2xx/3xx, every
# suite result must be fresher than the moment this run started, a page with no recorded
# answer counts against the verdict, and so does any scan status that is not ok, expected
# or random - an undeclared error, a page gone empty, a new page. Warnings stay reported
# but out of the verdict, since they compare against baselines another machine may not have.
#
# The host defaults to the local Apache mount; pass another as the first argument, e.g.
#   ./ci.sh http://127.0.0.1:8765/

. "$(dirname "$0")/home/home.sh"

host="${1:-http://localhost/pad/}"

started=$(date +%s)

status=$(curl -s -o /dev/null -w '%{http_code}' -L --max-time 600 "${host}regression/main/?index&test")

case "$status" in
  2*|3*) ;;
  *) echo "CI: ${host}regression/ answered HTTP ${status:-nothing}" >&2; exit 2 ;;
esac

exit=0

for suite in pages common framework; do

  file="$padHome/DATA/suites/$suite.json"

  [ -f "$file" ] || { echo "CI: no result for $suite" >&2; exit=1; continue; }

  summary=$(php -r 'echo json_decode(file_get_contents($argv[1]), true)["summary"] ?? "unreadable";' "$file")
  failed=$(php  -r 'echo json_decode(file_get_contents($argv[1]), true)["failed"]  ?? 1;'            "$file")
  newcnt=$(php  -r 'echo json_decode(file_get_contents($argv[1]), true)["new"]     ?? 0;'            "$file")
  when=$(php    -r 'echo json_decode(file_get_contents($argv[1]), true)["when"]    ?? 0;'            "$file")

  printf '%-12s %s\n' "$suite" "$summary"

  [ "$failed" = "0" ] || exit=1
  [ "$newcnt" = "0" ] || { echo "CI: $suite has $newcnt tests with no recorded answer" >&2; exit=1; }

  if [ "$when" -lt "$started" ]; then
    echo "CI: $suite result is from before this run started" >&2
    exit=1
  fi

done

warnings=$(grep -rlx "warning" "$padHome/DATA/regression" --include="*.txt" 2>/dev/null | wc -l | tr -d ' ')
attention=$(grep -rlxE "error|empty|new" "$padHome/DATA/regression" --include="*.txt" 2>/dev/null | wc -l | tr -d ' ')

echo "scan         $warnings warnings (not part of the verdict), $attention error/empty/new"

[ "$attention" = "0" ] || { echo "CI: the scan holds $attention pages on error, empty or new" >&2; exit=1; }

exit $exit
