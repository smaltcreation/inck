#!/bin/bash

# Supprimer le cache
sudo php app/console cache:clear --env=prod
sudo php app/console cache:clear

# Mettre à jour les vendors
sudo composer update

# Mettre à jour la base de données
sudo php app/console doctrine:schema:update --force
sudo php app/console doctrine:fixtures:load

# Linker les bundles dans le dossier web
sudo php app/console assets:install --symlink

# Mettre à jour les ressources
sudo rm -rf web/css/compiled web/js/compiled
sudo php app/console assetic:dump --env=prod
sudo php app/console assetic:dump