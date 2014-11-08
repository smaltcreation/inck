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
    const VIEW_NAME = 'InckNotificationBundle::subscriber.html.twig';

    /**
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="Symfony\Component\Security\Core\User\UserInterface")
     */
    private $subscriber;


    /**
     * @param UserInterface $subscriber
     * @param UserInterface $user
     */
    public function __construct($subscriber, $user)
    {
        parent::__construct();

        $this->subscriber   = $subscriber;
        $this->to           = $user;
    }

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

    /**
     * @return string
     */
    public function getViewName()
    {
        return self::VIEW_NAME;
    }
}
