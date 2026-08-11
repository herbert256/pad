#!/bin/sh

# Fault injection for ci.sh, the last link: each scenario builds a synthetic results
# directory, points the gate's trigger at a page that runs nothing, and asserts the exit
# code. One line speaks at the end; a broken scenario names itself.

. "$(dirname "$0")/../../../../../home/home.sh"

ci="$padHome/ci.sh"
tmp=$(mktemp -d)
trap 'rm -rf "$tmp"' EXIT

export CI_TRIGGER="http://localhost/pad/hello/?index"
export CI_RUN="gatetoken1234"

commit=$(git -C "$padHome" rev-parse --short HEAD 2>/dev/null)

suites="pages common errors framework regression sequence manual other"

fill () {
  when=$1
  for s in $suites; do
    printf '{"summary":"1 pages, 1 tests, 0 failed","failed":0,"new":0,"when":%s,"run":"%s","commit":"%s"}' \
      "$when" "$CI_RUN" "$commit" > "$tmp/$s.json"
  done
}

future () { echo $(( $(date +%s) + 5 )); }

run () { CI_SUITES="$tmp" "$ci" > /dev/null 2>&1; echo $?; }

broken=""

fill "$(future)"
[ "$(run)" = "0" ]                       || broken="$broken clean"

fill "$(future)"
printf '{"summary":"1 pages, 1 tests, 1 failed","failed":1,"new":0,"when":%s}' "$(future)" > "$tmp/manual.json"
[ "$(run)" != "0" ]                      || broken="$broken failed"

fill "$(future)"
printf '{"summary":"1 pages, 1 tests, 0 failed, 1 new","failed":0,"new":1,"when":%s}' "$(future)" > "$tmp/manual.json"
[ "$(run)" != "0" ]                      || broken="$broken new"

fill "$(future)"
rm "$tmp/sequence.json"
[ "$(run)" != "0" ]                      || broken="$broken missing"

fill "$(future)"
printf 'not json at all' > "$tmp/errors.json"
[ "$(run)" != "0" ]                      || broken="$broken corrupt"

fill "1000"
[ "$(run)" != "0" ]                      || broken="$broken stale"

fill "$(date +%s)"
[ "$(run)" != "0" ]                      || broken="$broken same-second"

fill "$(future)"
printf '{"summary":"1 pages, 1 tests, 0 failed","failed":0,"new":0,"when":%s,"run":"someoneelse","commit":"%s"}' \
  "$(future)" "$commit" > "$tmp/common.json"
[ "$(run)" != "0" ]                      || broken="$broken foreign-run"

fill "$(future)"
printf '{"summary":"1 pages, 1 tests, 0 failed","failed":0,"new":0,"when":%s,"run":"%s","commit":"badbad0"}' \
  "$(future)" "$CI_RUN" > "$tmp/pages.json"
[ "$(run)" != "0" ]                      || broken="$broken foreign-commit"

if [ -n "$broken" ]; then
  echo "GATE BROKEN:$broken"
else
  echo "the gate fails closed"
fi
