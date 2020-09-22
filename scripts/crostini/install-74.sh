#!/usr/bin/env bash

if [[ $EUID -ne 0 ]]; then
   echo "This script must be run as root" 
   exit 1
fi

apt update
apt -y upgrade
apt -y install nano mc 

su - herbert -c "ln -s /mnt/chromeos/GoogleDrive/MyDrive /home/herbert/google"
su - herbert -c "ln -s /mnt/chromeos/MyFiles/Downloads   /home/herbert/downloads"

echo '127.0.0.1 penguin.linux.test' >> /etc/hosts

wget -qO - https://download.sublimetext.com/sublimehq-pub.gpg | apt-key add -
echo "deb https://download.sublimetext.com/ apt/stable/" | tee /etc/apt/sources.list.d/sublime-text.list
apt update
apt -y install sublime-text

apt-key adv --fetch-keys 'https://mariadb.org/mariadb_release_signing_key.asc'
echo "deb http://mariadb.mirror.triple-it.nl/repo/10.5/debian buster main" | tee /etc/apt/sources.list.d/MariaDB.list
apt update
apt -y install mariadb-server

service mariadb start
sleep 1
echo "CREATE USER 'herbert'@'localhost' IDENTIFIED BY '';" | mariadb
echo "GRANT ALL PRIVILEGES ON *.* TO 'herbert'@'localhost'  WITH GRANT OPTION;" | mariadb
echo "FLUSH PRIVILEGES;" | mariadb

apt -y install apache2 

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

wget -O /etc/apt/trusted.gpg.d/php.gpg https://packages.sury.org/php/apt.gpg
echo "deb https://packages.sury.org/php/ buster main" | tee /etc/apt/sources.list.d/php.list
apt update
apt -y upgrade

apt -y install php7.4 libapache2-mod-php7.4 php7.4-{bcmath,bz2,curl,dba,enchant,gd,gmp,imap,interbase,intl,json,ldap,mbstring,mysql,odbc,opcache,pgsql,pspell,readline,soap,sqlite3,sybase,tidy,xml,xmlrpc,xsl,zip} php-pear

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
wget https://files.phpmyadmin.net/snapshots/phpMyAdmin-5.1+snapshot-english.tar.gz
tar xzvf phpMyAdmin-5.1+snapshot-english.tar.gz
rm phpMyAdmin-5.1+snapshot-english.tar.gz
mv phpMyAdmin-5.1+snapshot-english phpmyadmin
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

apt -y install memcached libmemcached-dev libyaml-dev
pecl channel-update pecl.php.net
printf "\n\n\n\n\n\n\n\n" | pecl install memcached
printf "\n\n\n\n\n\n\n\n" | pecl install memcached
printf "\n\n\n\n\n\n\n\n" | pecl install memcached
printf "\n\n\n\n\n\n"     | pecl install yaml
echo 'extension=yaml.so'      >> /usr/local/lib/php.ini
echo 'extension=memcached.so' >>  /usr/local/lib/php.ini

cd /tmp
wget http://www.webmin.com/download/deb/webmin-current.deb
apt -y install ./webmin-current.deb
rm webmin-current.deb

apt -y remove gcc make pkg-config apache2-dev libmemcached-dev libyaml-dev libtidy-dev libzip-dev libxslt1-dev libsodium-dev libxml2-dev libfreetype6-dev libonig-dev libpspell-dev libsqlite3-dev libssl-dev zlib1g-dev libbz2-dev libcurl4-gnutls-dev libpng-dev libwebp-dev libjpeg-dev libxpm-dev
apt -y autoremove
apt -y autoclean
apt -y clean

chown -R herbert:herbert /var/www
