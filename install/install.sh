#!/bin/bash

mysql < ../pad/doc/database.sql
mysql < ../pad/cache/cache.sql
mysql < ../apps/manual/database/demo.sql
mysql < ../apps/classicmodels/setup/classicmodels.sql

chmod 755 ../apps/manual/scripts/*