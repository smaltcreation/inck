#!/bin/bash

# Configuration de git ("file mode changed")
git config core.fileMode false

# Installation des paquets requis
sudo apt-get -y update

# Installation de Nginx/MySQL/cURL/acl
sudo apt-get -y install nginx mysql-server curl acl

# Création du dossier de cache pour les thumbs des articles
mkdir -p web/article web/media

# Paramêtrage des permissions
HTTPDUSER=`ps aux | grep -E '[a]pache|[h]ttpd|[_]www|[w]ww-data|[n]ginx' | grep -v root | head -1 | cut -d\  -f1`
setfacl -R -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs web/article web/media
setfacl -dR -m u:"$HTTPDUSER":rwX -m u:`whoami`:rwX app/cache app/logs web/article web/media

# Installation de PHP
sudo apt-get -y install php5-intl php-pear php5-memcache php5-curl php5-mysql php5-fpm php5-gd php5-zmq

# Installation de Composer
curl -sS https://getcomposer.org/installer | php
sudo mv composer.phar /usr/local/bin/composer
sudo composer install

# Installation des dépendances
sudo apt-get -y install nodejs npm zlibc memcached libxrender1 libxext6 zeromq-devel
sudo npm install less@1.7.5 -g
sudo pecl install memcache zmq-beta
sudo service nginx restart

# Lien symbolique vers nodejs
if [ ! -f /usr/local/bin/node ]; then
    sudo ln -s /usr/bin/nodejs /usr/local/bin/node
fi

# Creation de la DB, Installation des vendors
php app/console doctrine:database:create
sh app/update
