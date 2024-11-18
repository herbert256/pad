#!/bin/bash

if [[ $EUID -ne 0 ]]; then
	echo "Must be root"
	exit 1
fi

/pad/install/db.sh
/pad/install/data.sh
/pad/install/apache.sh
/pad/install/scripts.sh

service apache2 stop
service apache2 start
