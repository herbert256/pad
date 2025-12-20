#!/bin/bash

if [[ $EUID -ne 0 ]]; then
	echo "Must be root"
	exit 1
fi

/home/herbert/pad/pad/install/db.sh
/home/herbert/pad/pad/install/data.sh
/home/herbert/pad/pad/install/apache.sh
/home/herbert/pad/pad/install/scripts.sh

service apache2 stop
service apache2 start
