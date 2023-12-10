#!/bin/bash

if [[ $EUID -ne 0 ]]; then
	echo "Must be root"
	exit 1
fi

export usr=herbert

sed -i "s/var\/www/home\/$usr/g"                 /etc/apache2/apache2.conf
sed -i "s/var\/www\/html/home\/$usr\/pad\/www/g" /etc/apache2/sites-enabled/000-default.conf

chmod 755 /home/$usr/pad/apps/pad/_scripts/*

mysql < /home/$usr/pad/pad/install/database.sql
mysql < /home/$usr/pad/pad/cache/cache.sql
mysql < /home/$usr/pad/apps/pad/_config/demo.sql
mysql < /home/$usr/pad/apps/pad/miscellaneous/ClassicModels/database/classicmodels.sql

service apache2 stop
service apache2 start

mkdir /home/$usr/pad/data
chmod 777 /home/$usr/pad/data
ln -s /home/$usr/pad/data /home/$usr/pad/www/data
