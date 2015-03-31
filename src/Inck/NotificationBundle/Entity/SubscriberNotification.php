<?php

namespace Inck\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Inck\UserBundle\Entity\User;
use Inck\NotificationBundle\Model\NotificationInterface;

/**
 * SubscriberNotification
 *
 * @ORM\Table("subscriber_notification")
 * @ORM\Entity()
 */
class SubscriberNotification extends Notification implements NotificationInterface
{
    const VIEW_NAME = 'InckNotificationBundle:Notification:subscriber.html.twig';

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Inck\UserBundle\Entity\User")
     */
    private $subscriber;


    /**
     * @param User $subscriber
     * @param User $user
     */
    public function __construct(User $subscriber, User $user)
    {
        parent::__construct();

        $this->subscriber   = $subscriber;
        $this->to           = $user;
    }

    /**
     * @return User
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }

    /**
     * @param User $subscriber
     * @return $this
     */
    public function setSubscriber(User $subscriber)
    {
        $this->subscriber = $subscriber;

        return $this;
    }

    /**
     * @return string
     */
    public function getViewName()
    {
        return self::VIEW_NAME;
    }
}
