#!/bin/bash

# Mettre à jour les sources
git pull

# Metre à jour les vendors
composer self-update
composer update

# Supprimer le cache
php app/console cache:clear --env=prod
php app/console cache:clear

# Mettre à jour la base de données
php app/console doctrine:schema:update --force

if [ -z "$1" ]; then
    php app/console doctrine:fixtures:load
fi

# Mettre à jour les ressources
rm -rf web/css/compiled web/js/compiled
php app/console fos:js-routing:dump

if [ -z "$1" ]; then
    php app/console assetic:dump
elif [ $1 = "--env=prod" ]; then
    php app/console assetic:dump --env=prod --no-debug
fi

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
