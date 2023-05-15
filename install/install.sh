#!/bin/bash

export usr=herbert

if [[ $EUID -ne 0 ]]; then
	echo "Must be root"
	exit 1
fi

sed -i "s/var\/www/home\/$usr/g"                 /etc/apache2/apache2.conf
sed -i "s/var\/www\/html/home\/$usr\/pad\/www/g" /etc/apache2/sites-enabled/000-default.conf

chmod 755 /home/$usr/pad/apps/pad/scripts/*

mysql < /home/$usr/pad/install/database.sql
mysql < /home/$usr/pad/cache/cache.sql

mysql < /home/$usr/pad/apps/pad/config/demo.sql
mysql < /home/$usr/pad/apps/pad/miscellaneous/ClassicModels/database/classicmodels.sql

service apache2 stop
service apache2 start