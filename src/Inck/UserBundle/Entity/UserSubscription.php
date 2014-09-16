<?php

namespace Inck\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Inck\NotifBundle\Entity\Subscription;
use Inck\NotifBundle\Model\SubscriptionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * UserSubscription
 *
 * @ORM\Table()
 * @ORM\Entity()
 */
class UserSubscription extends Subscription implements SubscriptionInterface
{
    /**
     * @var UserInterface
     *
     * @ORM\OneToOne(targetEntity="Symfony\Component\Security\Core\User\UserInterface")
     */
    private $to;

    /**
     * @return UserInterface
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param UserInterface $entity
     */
    public function setTo($entity)
    {
        $this->to = $entity;
    }
}
