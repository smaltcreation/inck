#!/bin/bash

# Lien symbolique vers nodejs
if [ ! -f /usr/local/bin/node ]; then
    ln -s /usr/bin/nodejs /usr/local/bin/node
fi

# Configuration de git ("file mode changed")
git config core.fileMode false

# Metre à jour des vendors
composer self-update
composer update

# Supprimer le cache
php app/console cache:clear --env=prod

# Mettre à jour la base de données
php app/console doctrine:schema:update --force
php app/console doctrine:fixtures:load

# Mettre à jour les ressources
rm -rf web/css/compiled web/js/compiled
php app/console fos:js-routing:dump
php app/console assetic:dump --env=prod
php app/console assetic:dump
