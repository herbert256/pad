#!/bin/bash

mysql < pad/doc/database.sql
mysql < pad/cache/cache.sql
mysql < apps/manual/database/demo.sql
mysql < apps/classicmodels/setup/classicmodels.sql

chmod 755 apps/manual/scripts/*

rm -Rf /var/www/html
ln -s /app/www /var/www/html
chown data:data /var/www/html
