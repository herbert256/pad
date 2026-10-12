#!/bin/bash

. "$(dirname "$0")/../../home/home.sh"

exit;

rm -rf "$padHome/DATA"

mkdir "$padHome/DATA"

chown herbert:herbert "$padHome/DATA"
