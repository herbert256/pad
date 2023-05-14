#!/usr/bin/env bash

chmod 755 /Users/herbert/apps/pad/scripts/*

mysql < /Users/herbert/pad/install/database.sql
mysql < /Users/herbert/pad/cache/cache.sql

mysql < /Users/herbert/apps/pad/config/demo.sql
mysql < /Users/herbert/apps/pad/pages/miscellaneous/ClassicModels/database/classicmodels.sql 


brew services stop httpd
brew services stop mariadb

brew services start mariadb
brew services start httpd

brew install libyaml

/opt/homebrew/Cellar/libyaml/0.2.5/include/

/opt/homebrew/Cellar/libyaml/0.2.5/

