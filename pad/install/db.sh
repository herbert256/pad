#!/bin/bash

mysql < /home/herbert/pad/pad/database/database.sql
mysql < /home/herbert/pad/pad/database/pad.sql
mysql < /home/herbert/pad/pad/database/cache.sql

mysql < /home/herbert/apps/pad/_install/demo.sql
mysql < /home/herbert/apps/pad/_install/classicmodels.sql
