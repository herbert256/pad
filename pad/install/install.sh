#!/bin/bash

export usr=herbert

if [[ $EUID -ne 0 ]]; then
	echo "Must be root"
	exit 1
fi

sed -i "s/var\/www/home\/$usr/g"            /etc/apache2/apache2.conf
sed -i "s/var\/www\/html/home\/$usr\/www/g" /etc/apache2/sites-enabled/000-default.conf

chmod 755 /home/$usr/apps/pad/_scripts/*

mysql < /home/$usr/pad/install/database.sql
mysql < /home/$usr/pad/cache/cache.sql
mysql < /home/$usr/apps/pad/_config/demo.sql
mysql < /home/$usr/apps/pad/miscellaneous/ClassicModels/database/classicmodels.sql

service apache2 stop
service apache2 start

cd /home/$usr
mkdir data
cd /home/$usr/www
ln -s ../data data
