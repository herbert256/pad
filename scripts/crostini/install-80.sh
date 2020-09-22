#!/usr/bin/env bash

if [[ $EUID -ne 0 ]]; then
   echo "This script must be run as root" 
   exit 1
fi

apt-get update
apt-get -y upgrade
apt-get -y install nano mc apt-utils

su - herbert -c "ln -s /mnt/chromeos/GoogleDrive/MyDrive /home/herbert/google"
su - herbert -c "ln -s /mnt/chromeos/MyFiles/Downloads   /home/herbert/downloads"

echo '127.0.0.1 penguin.linux.test' >> /etc/hosts

wget -qO - https://download.sublimetext.com/sublimehq-pub.gpg | apt-key add -
echo "deb https://download.sublimetext.com/ apt/stable/" | tee /etc/apt/sources.list.d/sublime-text.list
apt-get update
apt-get -y install sublime-text

apt-key adv --fetch-keys 'https://mariadb.org/mariadb_release_signing_key.asc'
echo "deb http://mariadb.mirror.triple-it.nl/repo/10.5/debian buster main" | tee /etc/apt/sources.list.d/MariaDB.list
apt-get update
apt-get -y install mariadb-server

service mariadb start
sleep 1
echo "CREATE USER 'herbert'@'localhost' IDENTIFIED BY '';" | mariadb
echo "GRANT ALL PRIVILEGES ON *.* TO 'herbert'@'localhost'  WITH GRANT OPTION;" | mariadb
echo "FLUSH PRIVILEGES;" | mariadb

apt-get -y install gcc make pkg-config apache2 apache2-dev tidy libyaml-0-2 libaspell15 libpng16-16 libwebp6 libjpeg-progs libxpm4 libfreetype6 libonig5 libsodium23 libxslt1.1 libzip4 libtidy-dev libzip-dev libsodium-dev libxml2-dev libfreetype6-dev libonig-dev libpspell-dev libsqlite3-dev libssl-dev zlib1g-dev libbz2-dev libcurl4-gnutls-dev libpng-dev libwebp-dev libjpeg-dev libxpm-dev

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

cd /tmp
wget https://downloads.php.net/~pollita/php-8.0.0beta4.tar.xz
tar xf php*.xz
rm php*.xz
cd php*
./configure --with-apxs2=/usr/bin/apxs2 --with-mysql-sock=/run/mysqld/mysqld.sock --enable-mbstring --enable-exif --enable-ftp --with-zip --enable-soap --with-mysqli --with-pdo-mysql --with-curl --with-openssl --with-zlib --with-bz2 --enable-gd --with-webp --with-jpeg --with-xpm --with-freetype --with-tidy --with-xsl --with-sodium --enable-shmop --with-pspell --with-pear
make -j4
make install
cd ..
rm -rf php*

echo 'memory_limit = 2048M'        >  /usr/local/lib/php.ini
echo 'display_errors = on'         >> /usr/local/lib/php.ini
echo 'error_reporting = E_ALL'     >> /usr/local/lib/php.ini
echo 'zend_extension=opcache.so'   >> /usr/local/lib/php.ini
echo 'opcache.enable=1'            >> /usr/local/lib/php.ini
echo 'opcache.enable_cli=1'        >> /usr/local/lib/php.ini
echo 'opcache.jit_buffer_size=32M' >> /usr/local/lib/php.ini
echo 'opcache.jit=1235'            >> /usr/local/lib/php.ini

echo ' '                                    >> /etc/apache2/apache2.conf
echo '<FilesMatch "\.php">'                 >> /etc/apache2/apache2.conf
echo '  SetHandler application/x-httpd-php' >> /etc/apache2/apache2.conf
echo '</FilesMatch>'                        >> /etc/apache2/apache2.conf

a2enmod php
a2enmod info
a2enmod status
a2dismod mpm_event
a2enmod mpm_prefork

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

apt-get -y install memcached libmemcached-dev libyaml-dev
pecl channel-update pecl.php.net
printf "\n\n\n\n\n\n\n\n" | pecl install memcached
printf "\n\n\n\n\n\n\n\n" | pecl install memcached
printf "\n\n\n\n\n\n"     | pecl install yaml
echo 'extension=yaml.so'      >> /usr/local/lib/php.ini
echo 'extension=memcached.so' >>  /usr/local/lib/php.ini

cd /tmp
wget http://www.webmin.com/download/deb/webmin-current.deb
apt-get -y install ./webmin-current.deb
rm webmin-current.deb

apt-get -y remove gcc make pkg-config apache2-dev libmemcached-dev libyaml-dev libtidy-dev libzip-dev libxslt1-dev libsodium-dev libxml2-dev libfreetype6-dev libonig-dev libpspell-dev libsqlite3-dev libssl-dev zlib1g-dev libbz2-dev libcurl4-gnutls-dev libpng-dev libwebp-dev libjpeg-dev libxpm-dev
apt-get -y autoremove
apt-get -y autoclean
apt-get -y clean

chown -R herbert:herbert /var/www
