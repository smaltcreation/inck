#!/bin/bash

# Lien symbolique vers nodejs
if [ ! -f /usr/local/bin/node ]; then
    ln -s /usr/bin/nodejs /usr/local/bin/node
fi

# Configuration de git ("file mode changed")
git config core.fileMode false

# Installation des paquets requis
apt-get update

# Installtion de NGINX/MYSQL/CURL
apt-get install nginx mysql-server curl

# Installation de PHP
apt-get install php5-intl php-pear php5-memcache php5-curl php5-mysql php5-fpm

# Installation de Composer
curl -sS https://getcomposer.org/installer | php
mv composer.phar /usr/local/bin/composer

# Installation des dépendances
apt-get install nodejs npm zlibc memcached libxrender1 libxext6
npm install less@1.7.5 -g
pecl install memcache
service nginx restart

# Creation de la DB, Installation des vendors
php app/console doctrine:database:create 
sh app/update
