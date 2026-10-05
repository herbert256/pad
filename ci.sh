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
#
# CI_BENCH=25 ./ci.sh also fails when the pages got more than 25% slower (see the end).

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

# The runner keeps at most 16 letters and digits of the token, and it rides the trigger's
# query string as it is - a longer one, or one with + & # or a space, could never come back
# matching, and every suite failed as another run's.
case "$run" in
  ''|*[!A-Za-z0-9]*) echo "CI: the run token must be letters and digits only" >&2; exit 2 ;;
esac
[ "${#run}" -le 16 ] || { echo "CI: the run token is longer than 16 characters" >&2; exit 2; }
commit=$(git -C "$padHome" rev-parse --short HEAD 2>/dev/null)

# Checked before anything runs: a threshold that is no number would only fail at the end.
case "${CI_BENCH:-0}" in
  *[!0-9]*) echo "CI: CI_BENCH must be a whole percentage" >&2; exit 2 ;;
esac
case "${CI_BENCH_FLOOR:-5}" in
  ''|*[!0-9.]*) echo "CI: CI_BENCH_FLOOR must be a number of milliseconds" >&2; exit 2 ;;
esac

# Results are stamped in whole seconds, and a fast first suite can finish inside the very
# second the run started - which the strictly-newer test below reads as a leftover. The
# trigger fires once that second is over, so everything this run writes is newer. A
# doctored trigger runs no suite and writes nothing, so the test rig skips the wait - it
# runs this gate eleven times inside one request, under PHP's time limit.
[ -n "$CI_TRIGGER" ] || while [ "$(date +%s)" -eq "$started" ]; do sleep 0.05; done

status=$(curl -s -o /dev/null -w '%{http_code}' -L --max-time 600 "$trigger&ciRun=$run")

case "$status" in
  2*|3*) ;;
  *) echo "CI: ${host}regression/ answered HTTP ${status:-nothing}" >&2; exit 2 ;;
esac

exit=0

for suite in pages common errors framework regression sequence manual other; do

  file="$suitesDir/$suite.json"

  [ -f "$file" ] || { echo "CI: no result for $suite" >&2; exit=1; continue; }

  # One php reads every field the verdict needs. A php per field made a run slow enough
  # that the gate's own fault-injection case - nine runs inside one request - outran PHP's
  # time limit, and the killed request took the Framework suite down with it.
  { IFS= read -r summary; IFS= read -r failed; IFS= read -r newcnt
    IFS= read -r when;    IFS= read -r resRun; IFS= read -r resCommit; IFS= read -r tests; } < <(
    php -r '$r = json_decode(file_get_contents($argv[1]), true);
            echo $r["summary"] ?? "unreadable", "\n", $r["failed"] ?? 1,  "\n", $r["new"]    ?? 0,  "\n",
                 $r["when"]    ?? 0,            "\n", $r["run"]    ?? "", "\n", $r["commit"] ?? "", "\n",
                 count ( (array) ( $r["tests"] ?? [] ) ), "\n";' "$file")

  printf '%-12s %s\n' "$suite" "$summary"

  [ "$failed" = "0" ] || exit=1

  # A suite that ran nothing proves nothing: a walk directory gone or an application
  # renamed came back as "0 pages, 0 tests, 0 failed", which read as all well.
  [ "$tests" -gt 0 ] 2>/dev/null || { echo "CI: $suite ran no tests" >&2; exit=1; }
  [ "$newcnt" = "0" ] || { echo "CI: $suite has $newcnt tests with no recorded answer" >&2; exit=1; }

  # Strictly newer: a result stamped the very second the run started could as easily be
  # a leftover, and a real run takes seconds. Asked as "is it newer", so a stamp that is no
  # number at all - where test errors out and answers false - is refused too.
  if ! [ "$when" -gt "$started" ] 2>/dev/null; then
    echo "CI: $suite result is from before this run started" >&2
    exit=1
  fi

  if [ "$resRun" != "$run" ]; then
    echo "CI: $suite result belongs to another run" >&2
    exit=1
  fi

  # Held only when both sides know their commit - a runner without shell access stamps
  # nothing, and that is not a mismatch.
  if [ -n "$commit" ] && [ -n "$resCommit" ] && [ "$resCommit" != "$commit" ]; then
    echo "CI: $suite result was written on commit $resCommit, the tree stands on $commit" >&2
    exit=1
  fi

done

# The editor kits' completion lists are generated from pad/ by editors/generate.php: a tag,
# function, option or property added without regenerating them fails the gate like a
# failing suite. The gate's own test rig, which doctors the results directory, skips it.

if [ -z "$CI_SUITES" ]; then
  if php "$padHome/editors/generate.php" --check; then
    printf '%-12s %s\n' editors "completion lists match pad/"
  else
    exit=1
  fi
fi

# The editor tooling's own tests speak to it as an editor or an assistant would, against the
# fixture application in editors/fixture/apps: the language server over LSP, the MCP server
# over MCP - its pad_test runs this very script in the test rig's doctored world. The Tree-sitter
# check proves the committed parser is generated from grammar.js and the queries name only
# what the parser has, and runs the corpus when the tree-sitter CLI is installed. They need
# node - a machine without it cannot run the tooling either, and says so instead of failing.

if [ -z "$CI_SUITES" ]; then
  for tool in lsp:editors/lsp/test.js mcp:editors/mcp/test.js treesitter:editors/tree-sitter-pad/test/check.js; do
    if ! command -v node > /dev/null 2>&1; then
      printf '%-12s %s\n' "${tool%%:*}" "skipped - node is not installed"
    elif out=$(node "$padHome/${tool#*:}" 2>&1); then
      printf '%-12s %s\n' "${tool%%:*}" "$out"
    else
      printf '%s\n' "$out" >&2
      exit=1
    fi
  done
fi

# The applications' own tests - the _tests/ directory of every application, run by pad test
# on the command line (apps/cli/_commands/test.php): a failing test, or one without an
# answer, fails the gate like a failing suite. Skipped by the rig, like the editors line.

if [ -z "$CI_SUITES" ]; then
  if apptests=$(PAD_HOME="$padHome" php "$padHome/apps/cli/pad" test --all --brief); then
    printf '%-12s %s\n' apptests "$(printf '%s\n' "$apptests" | tail -1)"
  else
    printf '%s\n' "$apptests" >&2
    exit=1
  fi
fi

# The benchmark as a gate, when CI_BENCH names a threshold in percent: the develop app times
# every application page, keeps the run with this commit, and answers "benchmark slower"
# when the total or a page is that much slower than its median over the last runs - a page
# it names is timed again on its own first, and must have lost CI_BENCH_FLOOR ms (5) too.
# Off by default: times measured on a busy machine are a judgement call, not a fact.

if [ -n "$CI_BENCH" ] && [ -z "$CI_SUITES" ]; then
  bench=$(curl -s --max-time 600 "${host}develop/?benchmark/check&padInclude&threshold=$CI_BENCH&floor=${CI_BENCH_FLOOR:-5}")
  verdict=$(printf '%s\n' "$bench" | head -1)
  printf '%-12s %s\n' benchmark "$(printf '%s\n' "$bench" | sed -n 2p)"
  case "$verdict" in
    "benchmark ok") ;;
    "benchmark slower") printf '%s\n' "$bench" | tail -n +3 | sed 's/^/  /' >&2; exit=1 ;;
    *) echo "CI: the benchmark did not answer" >&2; exit=1 ;;
  esac
fi

exit $exit
