#!/bin/bash

mysql < /home/herbert/pad/pad/database/database.sql
mysql < /home/herbert/pad/pad/database/pad.sql
mysql < /home/herbert/pad/pad/database/cache.sql

mysql < /home/herbert/pad/apps/pad/_database/database.sql
mysql < /home/herbert/pad/apps/pad/_database/demo.sql
mysql < /home/herbert/pad/apps/pad/_database/classicmodels.sql
