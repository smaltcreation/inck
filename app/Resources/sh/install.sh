#!/bin/bash

# Lien symbolique vers nodejs
if [ ! -f /usr/local/bin/node ]; then
    ln -s /usr/bin/nodejs /usr/local/bin/node
fi

# Configuration de git ("file mode changed")
git config core.fileMode false

# Installation des paquets requis
apt-get -y update

# Installtion de Nginx/MySQL/cURL/acl
apt-get -y install nginx mysql-server curl acl

# Paramêtrage des permissions
HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
sudo setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs
sudo setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs

# Installation de PHP
apt-get -y install php5-intl php-pear php5-memcache php5-curl php5-mysql php5-fpm

# Installation de Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer
composer install

# Installation des dépendances
apt-get -y install nodejs npm zlibc memcached libxrender1 libxext6
npm install less@1.7.5 -g
pecl install memcache
service nginx restart

# Creation de la DB, Installation des vendors
php app/console doctrine:database:create 
sh app/update
