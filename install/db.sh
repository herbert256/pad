#!/bin/bash

mysql < /home/herbert/database/database.sql
mysql < /home/herbert/database/pad.sql
mysql < /home/herbert/database/cache.sql

mysql < /home/herbert/app/_database/database.sql
mysql < /home/herbert/app/_database/demo.sql
mysql < /home/herbert/app/_database/classicmodels.sql
