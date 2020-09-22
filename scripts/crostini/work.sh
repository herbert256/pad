#!/usr/bin/env bash

if [[ $EUID -ne 0 ]]; then
   exit 1
fi

apt-get update
apt-get -y upgrade
apt-get -y dist-upgrade
apt-get -y full-upgrade

apt-get -y install apache2 

a2enmod info
a2enmod status

sed -i 's/var\/www/home\/herbert/g'                           /etc/apache2/apache2.conf
sed -i 's/#ServerName www.example.com/ServerName localhost/g' /etc/apache2/sites-enabled/000-default.conf
sed -i 's/var\/www\/html/home\/herbert\/pad\/www/g'           /etc/apache2/sites-enabled/000-default.conf
sed -i 's/RUN_USER=www-data/RUN_USER=herbert/g'               /etc/apache2/envvars
sed -i 's/RUN_GROUP=www-data/RUN_GROUP=herbert/g'             /etc/apache2/envvars

echo 'ServerName localhost' >> /etc/apache2/apache2.conf

cat << EOT >> /etc/apache2/apache2.conf
<Location "/server-status">
    SetHandler server-status
    Require all granted
</Location>
EOT

cat << EOT >> /etc/apache2/apache2.conf
<Location "/server-info">
    SetHandler server-info
    Require all granted
</Location>
EOT

apt-get -y install php7.4 libapache2-mod-php7.4 php7.4-{bcmath,bz2,curl,dba,enchant,gd,gmp,imap,interbase,intl,json,ldap,mbstring,mysql,odbc,opcache,pgsql,pspell,readline,soap,sqlite3,sybase,tidy,xml,xmlrpc,xsl,zip} php-pear

a2dismod mpm_event
a2enmod  mpm_prefork
a2enmod  php

sed -i 's/memory_limit = 128M/memory_limit = 2048M/g'                                    /etc/php/7.4/apache2/php.ini
sed -i 's/display_errors = Off/display_errors = on/g'                                    /etc/php/7.4/apache2/php.ini
sed -i 's/error_reporting = E_ALL & ~E_DEPRECATED & ~E_STRICT/error_reporting = E_ALL/g' /etc/php/7.4/apache2/php.ini

echo ' '                                    >> /etc/apache2/apache2.conf
echo '<FilesMatch "\.php">'                 >> /etc/apache2/apache2.conf
echo '  SetHandler application/x-httpd-php' >> /etc/apache2/apache2.conf
echo '</FilesMatch>'                        >> /etc/apache2/apache2.conf

cd /var/www

wget -O - https://www.phpmyadmin.net/downloads/phpMyAdmin-latest-english.tar.xz | tar xJ
mv phpMyAdmin* phpmyadmin
cd /var/www/phpmyadmin
cp config.sample.inc.php config.inc.php
sed -i "s/secret'] = ''/secret'] = '73456dfggfddfgfdsdf54323456654323477'/g" config.inc.php
sed -i "s/AllowNoPassword'] = false/AllowNoPassword'] = true/g"              config.inc.php
mariadb < sql/create_tables.sql
cat << EOT >> /etc/apache2/apache2.conf
Alias /phpmyadmin /var/www/phpmyadmin
<Directory /var/www/phpmyadmin/>
  Options Indexes FollowSymLinks MultiViews
  AllowOverride All
  Require all granted
</Directory>
EOT

cd /var/www
wget https://github.com/phpsysinfo/phpsysinfo/archive/v3.3.2.tar.gz
tar zxvf v3.3.2.tar.gz
rm v3.3.2.tar.gz
mv phpsysinfo-3.3.2 phpsysinfo
cd /var/www/phpsysinfo
cp phpsysinfo.ini.new phpsysinfo.ini
cat << EOT >> /etc/apache2/apache2.conf
Alias /info /var/www/phpsysinfo
<Directory /var/www/phpsysinfo/>
  Options Indexes FollowSymLinks MultiViews
  AllowOverride All
  Require all granted
</Directory>
EOT

apt-get -y install memcached libmemcached-dev libyaml-dev
pecl channel-update pecl.php.net
printf "\n\n\n\n\n\n\n\n" | pecl install memcached
printf "\n\n\n\n\n\n\n\n" | pecl install memcached
printf "\n\n\n\n\n\n"     | pecl install yaml
echo 'extension=yaml.so'      >> /etc/php/7.4/apache2/php.ini
echo 'extension=memcached.so' >> /etc/php/7.4/apache2/php.ini

chown -R herbert:herbert /var/www
