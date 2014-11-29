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
memcached -u memcached -d -m 30 -l 127.0.0.1 -p 11211

# Creation de la DB, Installation des vendors

php app/console doctrine:database:create 
php app/console doctrine:schema:update --force
composer self-update
composer update
if [ -z "$1" ]; then
    php app/console assetic:dump
elif [ $1 = "--env=prod" ]; then
    php app/console assetic:dump --env=prod --no-debug
fi

# Serveur Ratchet

# Stopper Ratchet
echo "Stopping Ratchet..."
id=`ps aux | grep '[p]hp -f app/console inck:ratchet:start' | awk '{print $2}'`

if [ ! -z "$id" ]; then
    echo "$(tput setaf 4)kill $id$(tput sgr0)"
    kill $id
else
    echo "$(tput setaf 4)Ratchet is already stopped$(tput sgr0)"
fi

# Relancer Ratchet
echo "Starting Ratchet..."
php -f app/console inck:ratchet:start > /dev/null &

# Vérifier l'état de Ratchet
id=`ps aux | grep '[p]hp -f app/console inck:ratchet:start' | awk '{print $2}'`

if [ ! -z "$id" ]; then
    echo "$(tput setaf 2)Ratchet is on : pid = $id$(tput sgr0)"
else
    echo "$(tput setaf 1)Ratchet is off !$(tput sgr0)"
fi
