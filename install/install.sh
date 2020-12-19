#!/bin/bash

if [[ $EUID -ne 0 ]]; then
	echo "Must be root"
	exit 1
fi

service apache2 stop

sed -i 's/RUN_USER=www-data/RUN_USER=herbert/g'   /etc/apache2/envvars
sed -i 's/RUN_GROUP=www-data/RUN_GROUP=herbert/g' /etc/apache2/envvars

sed -i 's/var\/www/home\/herbert/g'               /etc/apache2/apache2.conf
sed -i 's/var\/www\/html/home\/herbert\/www/g'    /etc/apache2/sites-enabled/000-default.conf

mysql < /home/herbert/pad/install/database.sql
mysql < /home/herbert/pad/cache/cache.sql
mysql < /home/herbert/apps/manual/database/demo.sql
mysql < /home/herbert/apps/classicmodels/config/classicmodels.sql

chown -R herbert:herbert /var/www

service apache2 start
