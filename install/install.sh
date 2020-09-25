#!/bin/bash

sudo service apache2 stop

sudo sed -i 's/RUN_USER=www-data/RUN_USER=herbert/g'   /etc/apache2/envvars
sudo sed -i 's/RUN_GROUP=www-data/RUN_GROUP=herbert/g' /etc/apache2/envvars

sudo sed -i 's/var\/www/home\/herbert/g'            /etc/apache2/apache2.conf
sudo sed -i 's/var\/www\/html/home\/herbert\/www/g' /etc/apache2/sites-enabled/000-default.conf

sudo sed -i 's/\/home\/herbert\/php/\/var\/www\/php/g' /etc/apache2/apache2.conf

mysql --user mariadb < /home/herbert/pad/doc/database.sql
mysql --user mariadb < /home/herbert/pad/cache/cache.sql
mysql --user mariadb < /home/herbert/apps/manual/database/demo.sql
mysql --user mariadb < /home/herbert/apps/classicmodels/setup/classicmodels.sql

sudo chown -R herbert:herbert /var/www

sudo service apache2 start