#!/bin/bash

. "$(dirname "$0")/../../home/home.sh"

find "$padHome/apps" -path '*/_scripts/*' -type f -exec chmod 755 {} +
