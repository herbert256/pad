#!/bin/bash

mysql < /pad/database/database.sql
mysql < /pad/database/pad.sql
mysql < /pad/database/cache.sql

mysql < /app/_database/database.sql
mysql < /app/_database/demo.sql
mysql < /app/_database/classicmodels.sql
