#!/bin/bash

if [[ $EUID -ne 0 ]]; then
	echo "Must be root"
	exit 1
fi

chmod 755 /home/herbert/apps/reference/scripts/*

service apache2 stop

mysql < /home/herbert/pad/install/database.sql
mysql < /home/herbert/pad/cache/cache.sql
mysql < /home/herbert/apps/reference/config/demo.sql
mysql < /home/herbert/apps/manual/database/classicmodels.sql

service apache2 start