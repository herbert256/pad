#!/bin/bash
# Render the given .pad (or .html) page through the local PAD server (used by ⌘B).
#
# The application is the longest directory under apps/ that has an entry point
# www/<app>/index.php - regression/pages, not regression - and the page is the rest of the
# path, so a nested application's page is asked of that application. PAD_HOST overrides the
# server, http://localhost/pad/ by default.

f="$1"

# The checkout is the nearest directory above the file that holds pad/pad.php, as the
# language server finds it - cut at the first /apps/ of the path, a checkout that itself
# stands below a directory named apps (~/apps/pad) took that one for its own and found no
# entry point.

root=$(dirname "$f")
while [ "$root" != "/" ] && [ "$root" != "." ] && [ ! -f "$root/pad/pad.php" ]; do
  root=$(dirname "$root")
done

case "$f" in
  "$root"/apps/*) ;;
  *) echo "not inside apps/: $f" >&2; exit 1 ;;
esac

rel="${f#"$root"/apps/}"
rel="${rel%.pad}"
rel="${rel%.html}"
rel="${rel%.php}"

app=""
try=""
IFS='/' read -ra parts <<< "$rel"

for part in "${parts[@]:0:${#parts[@]}-1}"; do
  try="${try:+$try/}$part"
  [ -f "$root/www/$try/index.php" ] && app="$try"
done

if [ -z "$app" ]; then
  echo "no application entry point www/<app>/index.php for: $f" >&2
  exit 1
fi

item="${rel#"$app"/}"
host="${PAD_HOST:-http://localhost/pad/}"

url="${host%/}/$app/?$item&padInclude"

echo "GET $url"
echo
curl -s "$url"
