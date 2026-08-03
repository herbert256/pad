#!/bin/bash

if [[ $EUID -ne 0 ]]; then
	echo "Must be root"
	exit 1
fi

here="$(cd "$(dirname "$0")" && pwd)"

"$here/db.sh"
"$here/data.sh"
"$here/apache.sh"
"$here/scripts.sh"

service apache2 stop
service apache2 start
