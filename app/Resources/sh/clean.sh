#!/bin/bash

# Supprimer la base de données
php app/console doctrine:database:drop --force

# Recréer la base de données
php app/console doctrine:database:create
php app/console doctrine:schema:update --force

# Supprimer les images des articles
find web/article/image/ -type f -not -name '.gitkeep' | xargs rm -rf

# Supprimer les miniatures des images des articles
rm -rf web/media/cache/article_thumb/*
rm -rf web/media/cache/article_featured/*
