#!/bin/bash

service apache2 stop

sudo -Rf /var/www/html

sudo ln -s /home/herbert/www /var/www/html

sudo chown -R hgj:hgj /var/www

sed -i 's/RUN_USER=www-data/RUN_USER=herbert/g'   /etc/apache2/envvars
sed -i 's/RUN_GROUP=www-data/RUN_GROUP=herbert/g' /etc/apache2/envvars

mysql < /home/herbert/pad/doc/database.sql
mysql < /home/herbert/pad/cache/cache.sql
mysql < /home/herbert/apps/manual/database/demo.sql
mysql < /home/herbert/apps/classicmodels/setup/classicmodels.sql

sudo cat << EOT >> /etc/apache2/apache2.conf
<Directory /home/herbert/>
  Options Indexes FollowSymLinks MultiViews
  AllowOverride All
  Require all granted
</Directory>
EOT

sudo service apache2 start