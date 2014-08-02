Inck
====

L'expression libre et culturelle.

Allant de l'informatique jusqu'à la poésie en passant par la géopolitique et le divertissement, vous êtes le moteur
contribuant à la diversité des pensées et au partage de la connaissance. Lire, s'instruire, écrire, enseigner, divertir.
Ici tout est possible !

Bonnes pratiques
----------------

* Avoir un code propre
* Commenter
* Compléter l'UML
* Adapter ses balises HTML5 (<nav>, <article>)
* Avoir des titres de pages, des descriptions et un contenu variés de qualité
* Suivre les nouvelles normes des microdata http://schema.org/docs/gs.html
* Être parfait au niveau code pour le référencement

## Assetic

Afin de rester cohérent, voici la norme à respecter pour la compression des ressources : le fichier final (paramètre
__output__) doit être placé dans un dossier _type/compiled/bundle/directory_, et doit être nommé _view_.

Exemples pour le css (c'est bien sûr là même chose pour le js) :

|                    View                   |                 Output                 |
|:-----------------------------------------:|:--------------------------------------:|
| _InckCoreBundle:Default:index.html.twig_  | _css/compiled/core/default/index.css_  |
| _InckUserBundle:Security:login.html.twig_ | _css/compiled/user/security/login.css_ |
| _InckCoreBundle::layout.html.twig_        | _css/compiled/core/layout.css_         |
| _::base.html.twig_                        | _css/compiled/base.css_                |

Si vous avez besoin de compresser des ressources deux fois dans une seule vue, créez un dossier ayant le même nom que la
vue, et placez vos fichiers compressés dans ce dossier.

Par exemple, pour une vue _::base.html.twig_, au lieu d'avoir un fichier _base.css_, nous avons donc un dossier _base_.

|             Files              |            Output            |
|:------------------------------:|:----------------------------:|
| _lib1.min.css_, _lib2.min.css_ | _css/compiled/base/lib.css_  |
| _myfile1.css_, _myfile2.css_   | _css/compiled/base/main.css_ |

Configurer le projet
--------------------

La configuration suivante vous permet d'acceder au projet via les adresse suivantes : **inck.dev** et **inck.prod**.

## Étape 1

Créez un fichier __/etc/apache2/sites-available/inck.conf__ :

    <VirtualHost *:80>
        ServerName inck.dev
        ServerAlias www.inck.dev

        ServerAdmin admin@localhost
        DocumentRoot /var/www/inck/web

        <Directory /var/www/inck/web>
            Options Indexes FollowSymLinks MultiViews
            AllowOverride None
            Order allow,deny
            allow from all
            <IfModule mod_rewrite.c>
                RewriteEngine On
                RewriteCond %{REQUEST_FILENAME} !-f
                RewriteRule ^(.*)$ /app_dev.php [QSA,L]
            </IfModule>
        </Directory>

        LogLevel warn
        ErrorLog ${APACHE_LOG_DIR}/error-inck.dev.log
        CustomLog ${APACHE_LOG_DIR}/access-inck.dev.log combined
    </VirtualHost>

    <VirtualHost *:80>
        ServerName inck.prod
        ServerAlias www.inck.prod

        ServerAdmin admin@localhost
        DocumentRoot /var/www/inck/web

        <Directory /var/www/inck/web>
            Options Indexes FollowSymLinks MultiViews
            AllowOverride None
            Order allow,deny
            allow from all
            <IfModule mod_rewrite.c>
                RewriteEngine On
                RewriteCond %{REQUEST_FILENAME} !-f
                RewriteRule ^(.*)$ /app.php [QSA,L]
            </IfModule>
        </Directory>

        LogLevel warn
        ErrorLog ${APACHE_LOG_DIR}/error-inck.prod.log
        CustomLog ${APACHE_LOG_DIR}/access-inck.prod.log combined
    </VirtualHost>

Adaptez le chemins vers Inck si vous avez placé le projet dans un autre dossier.

## Étape 2

Activez ce fichier de configuration :

    a2ensite inck
    service apache2 reload

## Étape 3

Redirigez les noms de domaine vers votre machine. Dans le fichier __/etc/hosts__, ajoutez ces lignes :

    127.0.0.1	inck.dev
    127.0.0.1	inck.prod

Mettre à jour le projet
-----------------------

Après avoir mis à jour _(pull)_ votre version locale du projet, vous pouvez executer la commande `sh app/update` afin
de supprimer le cache et de mettre à jour les vendors, la base de données et les ressources.