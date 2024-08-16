#!/bin/bash

sed -i "s/var\/www/home\/$herbert/g"            /etc/apache2/apache2.conf
sed -i "s/var\/www\/html/home\/herbert\/www/g" /etc/apache2/sites-enabled/000-default.conf
