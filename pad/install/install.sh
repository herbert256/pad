#!/bin/bash

export usr=herbert

if [[ $EUID -ne 0 ]]; then
	echo "Must be root"
	exit 1
fi

sed -i "s/var\/www/home\/$usr\/pad/g"            /etc/apache2/apache2.conf
sed -i "s/var\/www\/html/home\/$usr\/pad\/www/g" /etc/apache2/sites-enabled/000-default.conf

chmod 755 /home/$usr/pad/apps/reference/scripts/*

mysql < /home/$usr/pad/pad/install/database.sql
mysql < /home/$usr/pad/pad/cache/cache.sql
mysql < /home/$usr/pad/apps/reference/config/demo.sql
mysql < /home/$usr/pad/apps/classicmodels/database/classicmodels.sql

service apache2 stop
service apache2 start