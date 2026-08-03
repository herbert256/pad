#!/bin/bash

. "$(dirname "$0")/../../home/home.sh"

sed -i "s|/var/www|$padHome/www|g"      /etc/apache2/apache2.conf
sed -i "s|/var/www/html|$padHome/www|g" /etc/apache2/sites-enabled/000-default.conf