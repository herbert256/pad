#!/usr/bin/env bash

# The suites as a gate: run everything, read what the runs left in DATA/suites/, and exit
# nonzero when anything failed - which is what a git hook or a CI step can act on.
#
# The gate fails closed: the trigger must answer 2xx/3xx, every
# suite result must be fresher than the moment this run started, and a page with no
# recorded answer counts against the verdict.
#
# The host defaults to the local Apache mount; pass another as the first argument, e.g.
#   ./ci.sh http://127.0.0.1:8765/

. "$(dirname "$0")/home/home.sh"

host="${1:-http://localhost/pad/}"

started=$(date +%s)

# The gate's own test rig points these at a doctored world - a cheap trigger, a synthetic
# results directory, a known token - so every refusal below can be proven without a real
# run.
trigger="${CI_TRIGGER:-${host}regression/main/?index&test}"
suitesDir="${CI_SUITES:-$padHome/DATA/suites}"

# Every run has an identity: the token rides the trigger, the runner stamps it into each
# result, and a result that carries another token - a concurrent run, a stray browser
# Test - is not this run's verdict. The commit binds the results to what was tested.
run="${CI_RUN:-$(php -r 'echo bin2hex(random_bytes(6));')}"
commit=$(git -C "$padHome" rev-parse --short HEAD 2>/dev/null)

status=$(curl -s -o /dev/null -w '%{http_code}' -L --max-time 600 "$trigger&ciRun=$run")

case "$status" in
  2*|3*) ;;
  *) echo "CI: ${host}regression/ answered HTTP ${status:-nothing}" >&2; exit 2 ;;
esac

exit=0

for suite in pages common errors framework regression sequence manual other; do

  file="$suitesDir/$suite.json"

  [ -f "$file" ] || { echo "CI: no result for $suite" >&2; exit=1; continue; }

  summary=$(php -r 'echo json_decode(file_get_contents($argv[1]), true)["summary"] ?? "unreadable";' "$file")
  failed=$(php  -r 'echo json_decode(file_get_contents($argv[1]), true)["failed"]  ?? 1;'            "$file")
  newcnt=$(php  -r 'echo json_decode(file_get_contents($argv[1]), true)["new"]     ?? 0;'            "$file")
  when=$(php    -r 'echo json_decode(file_get_contents($argv[1]), true)["when"]    ?? 0;'            "$file")

  printf '%-12s %s\n' "$suite" "$summary"

  [ "$failed" = "0" ] || exit=1
  [ "$newcnt" = "0" ] || { echo "CI: $suite has $newcnt tests with no recorded answer" >&2; exit=1; }

  # Strictly newer: a result stamped the very second the run started could as easily be
  # a leftover, and a real run takes seconds.
  if [ "$when" -le "$started" ]; then
    echo "CI: $suite result is from before this run started" >&2
    exit=1
  fi

  resRun=$(php -r 'echo json_decode(file_get_contents($argv[1]), true)["run"] ?? "";' "$file")

  if [ "$resRun" != "$run" ]; then
    echo "CI: $suite result belongs to another run" >&2
    exit=1
  fi

  # Held only when both sides know their commit - a runner without shell access stamps
  # nothing, and that is not a mismatch.
  resCommit=$(php -r 'echo json_decode(file_get_contents($argv[1]), true)["commit"] ?? "";' "$file")

  if [ -n "$commit" ] && [ -n "$resCommit" ] && [ "$resCommit" != "$commit" ]; then
    echo "CI: $suite result was written on commit $resCommit, the tree stands on $commit" >&2
    exit=1
  fi

done

exit $exit
