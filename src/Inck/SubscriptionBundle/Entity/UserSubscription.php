<?php

namespace Inck\SubscriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Inck\SubscriptionBundle\Model\SubscriptionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * UserSubscription
 *
 * @ORM\Table("user_subscription")
 * @ORM\Entity()
 */
class UserSubscription extends Subscription implements SubscriptionInterface
{
    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="Symfony\Component\Security\Core\User\UserInterface")
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
     * @return $this
     */
    public function setTo($entity)
    {
        $this->to = $entity;

        return $this;
    }
}
