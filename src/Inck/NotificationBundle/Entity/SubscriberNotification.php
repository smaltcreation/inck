<?php

namespace Inck\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Inck\NotificationBundle\Model\NotificationInterface;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * SubscriberNotification
 *
 * @ORM\Table()
 * @ORM\Entity
 */
class SubscriberNotification extends Notification implements NotificationInterface
{
    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="Symfony\Component\Security\Core\User\UserInterface")
     */
    protected $subscriber;

    /**
     * @return UserInterface
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * @param UserInterface $subscriber
     * @return $this
     */
    public function setSubscriber($subscriber)
    {
        $this->subscriber = $subscriber;

        return $this;
    }
}
