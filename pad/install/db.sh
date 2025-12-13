#!/bin/bash

mysql < /home/herbert/pad/database/database.sql
mysql < /home/herbert/pad/database/pad.sql
mysql < /home/herbert/pad/database/cache.sql

mysql < /home/herbert/app/_database/database.sql
mysql < /home/herbert/app/_database/demo.sql
mysql < /home/herbert/app/_database/classicmodels.sql
