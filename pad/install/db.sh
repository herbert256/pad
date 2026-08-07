#!/bin/bash

. "$(dirname "$0")/../../home/home.sh"

mysql < "$padHome/pad/database/database.sql"
mysql < "$padHome/pad/database/pad.sql"
mysql < "$padHome/pad/database/cache.sql"

mysql < "$padHome/apps/pad/_install/demo.sql"
mysql < "$padHome/apps/classicModels/_install/classicmodels.sql"
