#!/bin/bash

mysql < /data/pad/doc/database.sql
mysql < /data/pad/cache/cache.sql
mysql < /data/apps/manual/database/demo.sql
mysql < /data/apps/classicmodels/setup/classicmodels.sql