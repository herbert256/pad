#!/bin/bash

mysql < /home/herbert/pad/pad/database/database.sql
mysql < /home/herbert/pad/pad/database/pad.sql
mysql < /home/herbert/pad/pad/database/cache.sql

mysql < /home/herbert/pad/app/_database/database.sql
mysql < /home/herbert/pad/app/_database/demo.sql
mysql < /home/herbert/pad/app/_database/classicmodels.sql
