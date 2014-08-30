<?php

namespace Inck\NotifBundle\Entity;

use Doctrine\ORM\EntityRepository;
use Inck\UserBundle\Entity\User;

/**
 * SubscriptionRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class SubscriptionRepository extends EntityRepository
{
    /**
     * @param $entity string
     * @param $id int
     * @return int
     */
    public function countByEntity($entity, $id)
    {
        $query = $this
            ->createQueryBuilder('s')
            ->select('COUNT(s)')
            ->where('s.entityName = :entity')
            ->setParameter('entity', $entity)
            ->andWhere('s.entityId = :id')
            ->setParameter('id', $id)
            ->getQuery()
        ;

        return (int) $query->getSingleScalarResult();
    }

    /**
     * @param $entity string
     * @param $id int
     * @param $user User
     * @return mixed Subscription|null
     */
    public function getByEntityAndUser($entity, $id, $user)
    {
        $query = $this
            ->createQueryBuilder('s')
            ->select('s')
            ->where('s.entityName = :entity')
            ->setParameter('entity', $entity)
            ->andWhere('s.entityId = :id')
            ->setParameter('id', $id)
            ->andWhere('s.subscriber = :user')
            ->setParameter('user', $user)
            ->getQuery()
        ;

        return $query->getOneOrNullResult();
    }
}