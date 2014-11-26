#!/bin/bash

# Lien symbolique vers nodejs
if [ ! -f /usr/local/bin/node ]; then
    ln -s /usr/bin/nodejs /usr/local/bin/node
fi

# Configuration de git ("file mode changed")
git config core.fileMode false

# Installation des paquets requis
apt-get update
apt-get install nodejs npm zlibc php-pear php5-memcache memcached

npm install less@1.7.5 -g
pecl install memcache

service nginx restart
memcached -u memcached -d -m 30 -l 127.0.0.1 -p 11211
