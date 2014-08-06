<?php

namespace Inck\ArticleBundle\Entity;

use Doctrine\ORM\EntityRepository;

/**
 * TagRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class TagRepository extends EntityRepository
{
    public function getAutocompleteResults($name)
    {
        $query = $this
            ->createQueryBuilder('t')
            ->select('t.name')
            ->where('t.name LIKE :name')
            ->setParameter('name', "%$name%")
            ->getQuery()
        ;

        return $query->getResult();
    }

    public function findWhereNameIn($names)
    {
        $query = $this
            ->createQueryBuilder('t')
            ->where('t.name IN (:names)')
            ->setParameter('names', $names)
            ->getQuery()
        ;

        return $query->getResult();
    }
}