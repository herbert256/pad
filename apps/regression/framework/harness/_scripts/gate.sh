#!/bin/sh

# Fault injection for ci.sh, the last link: each scenario builds a synthetic results
# directory, points the gate's trigger at a page that runs nothing, and asserts the exit
# code. One line speaks at the end; a broken scenario names itself. Each planted result
# carries this run's token and commit, so a scenario fails for the one reason it names -
# without them the failed and new cases were refused as another run's, whatever the
# failed and new checks did.
#
# The first argument is the base the applications are served under, the $padHost of the
# page that runs this - {script:gate $gateHost} passes it. The trigger was fixed at
# http://localhost/pad/, so the script failed wherever www/ is the docroot or the host
# has another name.

. "$(dirname "$0")/../../../../../home/home.sh"

ci="$padHome/ci.sh"
tmp=$(mktemp -d)
trap 'rm -rf "$tmp"' EXIT

host="${1:-http://localhost/pad/}"

export CI_TRIGGER="${host}hello/?index"
export CI_RUN="gatetoken1234"

commit=$(git -C "$padHome" rev-parse --short HEAD 2>/dev/null)

suites="pages common errors framework regression sequence manual other"

fill () {
  when=$1
  for s in $suites; do
    printf '{"summary":"1 pages, 1 tests, 0 failed","failed":0,"new":0,"when":%s,"run":"%s","commit":"%s","tests":[{"status":"ok"}]}' \
      "$when" "$CI_RUN" "$commit" > "$tmp/$s.json"
  done
}

future () { echo $(( $(date +%s) + 5 )); }

run () { CI_SUITES="$tmp" "$ci" > /dev/null 2>&1; echo $?; }

broken=""

fill "$(future)"
[ "$(run)" = "0" ]                       || broken="$broken clean"

fill "$(future)"
printf '{"summary":"1 pages, 1 tests, 1 failed","failed":1,"new":0,"when":%s,"run":"%s","commit":"%s","tests":[{"status":"ok"}]}' \
  "$(future)" "$CI_RUN" "$commit" > "$tmp/manual.json"
[ "$(run)" != "0" ]                      || broken="$broken failed"

fill "$(future)"
printf '{"summary":"1 pages, 1 tests, 0 failed, 1 new","failed":0,"new":1,"when":%s,"run":"%s","commit":"%s","tests":[{"status":"ok"}]}' \
  "$(future)" "$CI_RUN" "$commit" > "$tmp/manual.json"
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

fill '"later"'
[ "$(run)" != "0" ]                      || broken="$broken garbled-when"

fill "$(future)"
printf '{"summary":"0 pages, 0 tests, 0 failed","failed":0,"new":0,"when":%s,"run":"%s","commit":"%s","tests":[]}' \
  "$(future)" "$CI_RUN" "$commit" > "$tmp/other.json"
[ "$(run)" != "0" ]                      || broken="$broken no-tests"

fill "$(future)"
printf '{"summary":"1 pages, 1 tests, 0 failed","failed":0,"new":0,"when":%s,"run":"someoneelse","commit":"%s","tests":[{"status":"ok"}]}' \
  "$(future)" "$commit" > "$tmp/common.json"
[ "$(run)" != "0" ]                      || broken="$broken foreign-run"

fill "$(future)"
printf '{"summary":"1 pages, 1 tests, 0 failed","failed":0,"new":0,"when":%s,"run":"%s","commit":"badbad0","tests":[{"status":"ok"}]}' \
  "$(future)" "$CI_RUN" > "$tmp/pages.json"
[ "$(run)" != "0" ]                      || broken="$broken foreign-commit"

if [ -n "$broken" ]; then
  echo "GATE BROKEN:$broken"
else
  echo "the gate fails closed"
fi
