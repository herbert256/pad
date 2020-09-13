#!/bin/bash

sudo service mariadb start
sleep 1

mysql < /data/pad/doc/database.sql
mysql < /data/pad/cache/cache.sql
mysql < /data/apps/manual/database/demo.sql
mysql < /data/apps/classicmodels/setup/classicmodels.sql

sudo service mariadb stop

chmod 755 /data/apps/manual/scripts/*
