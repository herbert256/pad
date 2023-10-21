#!/bin/bash

if [[ $EUID -ne 0 ]]; then
	echo "Must be root"
	exit 1
fi

export usr=herbert

sed -i "s/var\/www/home\/$usr/g"                 /etc/apache2/apache2.conf
sed -i "s/var\/www\/pad/home\/$usr\/pad\/www/g" /etc/apache2/sites-enabled/000-default.conf

chmod 755 ~$usr/pad/apps/pad/_scripts/*

mysql < ~$usr/pad/pad/install/database.sql
mysql < ~$usr/pad/pad/cache/cache.sql
mysql < ~$usr/pad/apps/pad/_config/demo.sql
mysql < ~$usr/pad/apps/pad/miscellaneous/ClassicModels/database/classicmodels.sql

service apache2 stop
service apache2 start

mkdir ~$usr/pad/data
ln -s ~$usr/pad/data ~$usr/pad/www/data
