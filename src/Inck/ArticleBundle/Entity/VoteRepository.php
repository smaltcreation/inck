<?php

namespace Inck\ArticleBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Inck\UserBundle\Entity\User;

/**
 * VoteRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class VoteRepository extends EntityRepository
{
    /**
     * @param $article Article
     * @param $up bool
     * @return int
     */
    public function countByArticle($article, $up)
    {
        $query = $this
            ->createQueryBuilder('v')
            ->select('COUNT(v)')
            ->where('v.article = :article')
            ->setParameter('article', $article)
            ->andWhere('v.up = :up')
            ->setParameter('up', $up)
            ->getQuery()
        ;

        return (int) $query->getSingleScalarResult();
    }

    /**
     * @param $article Article
     * @param $user User
     * @return mixed Vote|null
     */
    public function getByArticleAndUser($article, $user)
    {
        $query = $this
            ->createQueryBuilder('v')
            ->select('v')
            ->where('v.article = :article')
            ->setParameter('article', $article)
            ->andWhere('v.user = :user')
            ->setParameter('user', $user)
            ->getQuery()
        ;

        return $query->getOneOrNullResult();
    }

    /**
     * @param string $filterName
     * @param string $columnName
     * @param string $join
     * @return string
     */
    public function getOrderFilterQuery($filterName, $columnName, $join)
    {
        $qb = $this->createQueryBuilder($join)
            ->select("COUNT($join.id)")
            ->innerJoin("$join.article", $join.'Va')
            ->where($join.'Va.id = a.id')
            ->andWhere("$join.up = :$filterName");

        return sprintf('(%s) AS %s', $qb->getDQL(), $columnName);
    }
}
