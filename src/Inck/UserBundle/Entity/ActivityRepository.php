<?php

namespace Inck\UserBundle\Entity;
use Inck\UserBundle\Controller\ActivityController;

/**
 * ActivityRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class ActivityRepository extends \Doctrine\ORM\EntityRepository
{
    public function findPrivateByUser(User $user)
    {
        $em = $this->getEntityManager();
        $qb = $em->createQueryBuilder();

        $q  = $qb->select(array('a'))
            ->from('InckUserBundle:Activity', 'a')
            ->where('a.visibility != :visibility')
            ->andWhere('a.user = :user')
            ->setParameter('visibility', Activity::VISIBILITY_HIDDEN)
            ->setParameter('user', $user)
            ->setMaxResults(20)
            ->orderBy('a.createdAt', 'DESC')
            ->getQuery();

        return $q->getResult();
    }
}