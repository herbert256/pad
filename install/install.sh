#!/bin/bash

if [[ $EUID -ne 0 ]]; then
	echo "Must be root"
	exit 1
fi


chmod 755 /home/herbert/apps/reference/scripts/*

service apache2 stop

rm /home/herbert/www/index.php
cp /home/herbert/pad/install/www.php /home/herbert/www/index.php

mysql < /home/herbert/pad/install/database.sql
mysql < /home/herbert/pad/cache/cache.sql
mysql < /home/herbert/apps/pad/database/demo.sql
mysql < /home/herbert/apps/classicmodels/config/classicmodels.sql

chown -R herbert:herbert /var/www

service apache2 start