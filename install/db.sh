#!/bin/bash

mysql < /home/$usr/pad/pad/database/database.sql
mysql < /home/$usr/pad/pad/database/pad.sql
mysql < /home/$usr/pad/pad/database/cache.sql

mysql < /home/$usr/pad/apps/pad/_database/database.sql
mysql < /home/$usr/pad/apps/pad/_database/demo.sql
mysql < /home/$usr/pad/apps/pad/_database/classicmodels.sql
