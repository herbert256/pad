#!/bin/bash
# Render the given .pad page through the local PAD server (used by ⌘B).

f="$1"

case "$f" in
  */apps/*) ;;
  *) echo "not inside apps/: $f" >&2; exit 1 ;;
esac

rel="${f#*/apps/}"
app="${rel%%/*}"
item="${rel#*/}"
item="${item%.pad}"

url="http://localhost/pad/$app/?$item&padInclude"

echo "GET $url"
echo
curl -s "$url"
