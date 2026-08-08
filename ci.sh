#!/usr/bin/env bash

# The suites as a gate: run everything, read what the runs left in DATA/suites/, and exit
# nonzero when anything failed - which is what a git hook or a CI step can act on. The scan
# is not part of the verdict: its warnings compare against baselines a machine may not have,
# so they are reported but do not fail the gate.
#
# The host defaults to the local Apache mount; pass another as the first argument, e.g.
#   ./ci.sh http://127.0.0.1:8765/

. "$(dirname "$0")/home/home.sh"

host="${1:-http://localhost/pad/}"

curl -sL --max-time 600 "${host}regression/?index&test" -o /dev/null || {
  echo "CI: could not reach ${host}regression/" >&2
  exit 2
}

exit=0

for suite in tags functions options properties expressions variables data prefixes escaping custom check sequence pages common; do

  file="$padHome/DATA/suites/$suite.json"

  [ -f "$file" ] || { echo "CI: no result for $suite" >&2; exit=1; continue; }

  summary=$(php -r 'echo json_decode(file_get_contents($argv[1]), true)["summary"] ?? "unreadable";' "$file")
  failed=$(php  -r 'echo json_decode(file_get_contents($argv[1]), true)["failed"]  ?? 1;'            "$file")

  printf '%-12s %s\n' "$suite" "$summary"

  [ "$failed" = "0" ] || exit=1

done

warnings=$(grep -rlx "warning" "$padHome/DATA/regression" --include="*.txt" 2>/dev/null | wc -l | tr -d ' ')
echo "scan         $warnings warnings (not part of the verdict)"

exit $exit
