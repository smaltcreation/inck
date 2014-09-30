# Comment utiliser la méthode [findByFilters](https://github.com/samybob1/inck/blob/master/src/Inck/ArticleBundle/Entity/ArticleRepository.php#L36) ?

## Argument 1 : $filters

Cet argument est requis, et doit être un tableau.

| Nom du filtre                   	| Valeur acceptée                                                                             	|
|---------------------------------	|---------------------------------------------------------------------------------------------	|
| `type`                          	| string : `as_draft`, `published`, `posted`, `in_moderation`, `in_validation`, `disapproved` 	|
| `authors`, `categories`, `tags` 	| array : identifiants                                                                        	|
| `author`, `categorie`, `tag`    	| object : `User`, `Category`, `Tag`                                                          	|
| `search`                        	| string : terme recherché                                                                    	|
| `order`                         	| string : `vote` pour trier par popularité                                                   	|
| `not`                           	| int : identifiant de l'article qu'on ne veut pas avoir dans les résultats                   	|

Pour obtenir les articles **publiés** de l'utilisateur **$user** :

```
$articles = $repository->findByFilters(array(
    'type'      => 'published',
    'author'    => $user,
));
```

Nous obtenons uniquement la page 1
(les [n](https://github.com/samybob1/inck/blob/master/src/Inck/ArticleBundle/Entity/ArticleRepository.php#L18)
premiers articles), parce que l'argument 2 n'est pas défini.

## Argument 2 : $page

Cet argument est optionnel, et doit être un entier ou `false`. Il vaut par défaut 1.

En modifiant un peu l'exemple précédent, nous pouvons récupérer la **page 2**
(les [n](https://github.com/samybob1/inck/blob/master/src/Inck/ArticleBundle/Entity/ArticleRepository.php#L18)
articles suivants) :

```
$articles = $repository->findByFilters(array(
    'type'      => 'published',
    'author'    => $user,
), 2);
```

En passant `false`, nous pouvont aussi récupérer **tous les articles** :

```
$articles = $repository->findByFilters(array(
    'type'      => 'published',
    'author'    => $user,
), false);
```

## Argument 3 : $limit

Cet argument est optionnel, et doit être un entier ou `false`. Il  vaut par défaut `false`. Si différent de `false`,
il permet de limiter le nombre de résultats.

## Plus d'exemples

Pour obtenir **le dernier** article publié :

```
$articles = $repository->findByFilters(array(
    'type'  => 'published',
), false, 1);
```

Pour obtenir la **page 1**
(les [n](https://github.com/samybob1/inck/blob/master/src/Inck/ArticleBundle/Entity/ArticleRepository.php#L18)
premiers articles) des articles **publiés** associés aux tags "tag1" (id 1) et "tag2" (id 2) :

```
$articles = $repository->findByFilters(array(
    'type'  => 'published',
    'tags'  => array(1, 2),
));
```
