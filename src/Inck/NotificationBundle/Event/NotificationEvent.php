<?php

namespace Inck\NotificationBundle\Event;

use Inck\NotificationBundle\Model\NotificationInterface;
use Symfony\Component\EventDispatcher\Event;

class NotificationEvent extends Event
{
    /**
     * @var NotificationInterface
     */
    protected $notification;

    public function __construct(NotificationInterface $notification)
    {
        $this->notification = $notification;
    }

    /**
     * @return NotificationInterface
     */
    public function getNotification()
    {
        return $this->notification;
    }
}
