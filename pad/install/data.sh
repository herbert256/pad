#!/bin/bash

. "$(dirname "$0")/../../home/home.sh"

exit;

rm -rf "$padHome/DATA"
rm -rf "$padHome/www/DATA"

mkdir "$padHome/DATA"
ln -s "$padHome/DATA" "$padHome/www/DATA"

chown herbert:herbert "$padHome/DATA"
chown herbert:herbert "$padHome/www/DATA"
