#!/bin/bash

# Metre à jour des vendors
#composer self-update
#composer update

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
