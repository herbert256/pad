#!/bin/bash

sudo service mariadb start
sleep 3

mysql < pad/doc/database.sql
mysql < pad/cache/cache.sql
mysql < apps/manual/database/demo.sql
mysql < apps/classicmodels/setup/classicmodels.sql

sudo service mariadb stop

chmod 755 apps/manual/scripts/*
