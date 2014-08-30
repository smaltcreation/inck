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
    /**
     * @param array $name
     * @return array
     */
    public function getAutocompleteResults($name)
    {
        $query = $this
            ->createQueryBuilder('t')
            ->select('t.id', 't.name')
            ->where('t.name LIKE :name')
            ->setParameter('name', "%$name%")
            ->getQuery()
        ;

        return $query->getResult();
    }

    /**
     * @param array $names
     * @return array
     */
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

    /**
     * @param string $filterName
     * @param string $columnName
     * @return string
     */
    public function getScoreFilterQuery($filterName, $columnName)
    {
        $qb = $this->createQueryBuilder('t')
            ->select('COUNT(t.id)')
            ->innerJoin('t.articles', 'ta')
            ->where('ta.id = a.id')
            ->andWhere("t.id IN (:$filterName)");

        return sprintf('(%s) AS %s', $qb->getDQL(), $columnName);
    }
}
