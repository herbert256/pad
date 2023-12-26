#!/bin/bash

if [[ $EUID -ne 0 ]]; then
	echo "Must be root"
	exit 1
fi

export usr=herbert

/home/$usr/pad/pad/install/db.sh
/home/$usr/pad/pad/install/data.sh
/home/$usr/pad/pad/install/apache.sh
/home/$usr/pad/pad/install/scripts.sh

service apache2 stop
service apache2 start
