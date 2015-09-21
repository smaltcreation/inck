<?php

namespace Inck\UserBundle\Entity;

use Doctrine\ORM\EntityRepository;


class BadgeRepository extends EntityRepository
{

    /**
     * @param User $user
     * @return array
     */
    public function getPertinentBadges($user)
    {
        dump([$user->getId()]);
        $query = $this
            ->createQueryBuilder('b')
            ->select('b')
            ->where('b.users IN (:user)')
            ->orderBy('b.lvl', 'DESC')
            ->setMaxResults('2')
            ->setParameter('user', [$user->getId()])
            ->getQuery()
        ;

        return $query->getResult();
    }
}