#!/bin/bash

# Supprimer le cache
composer self-update
composer update
php app/console cache:clear --env=prod
php app/console cache:clear

# Mettre à jour les vendors
composer update

# Mettre à jour la base de données
php app/console doctrine:schema:update --force
php app/console doctrine:fixtures:load

# Linker les bundles dans le dossier web
php app/console assets:install --symlink

# Mettre à jour les ressources
rm -rf web/css/compiled web/js/compiled
php app/console assetic:dump --env=prod
php app/console assetic:dump
