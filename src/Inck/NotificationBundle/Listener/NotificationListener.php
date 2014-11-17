<?php

namespace Inck\NotificationBundle\Listener;

use Doctrine\Common\Persistence\ObjectManager;
use Inck\NotificationBundle\Entity\SubscriberNotification;
use Inck\NotificationBundle\Event\NotificationEvent;
use Inck\NotificationBundle\Manager\NotificationManager;

class NotificationListener
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var NotificationManager
     */
    private $notificationManager;


    /**
     * @param ObjectManager $em
     * @param NotificationManager $notificationManager
     */
    public function __construct(ObjectManager $em, NotificationManager $notificationManager)
    {
        $this->em = $em;
        $this->notificationManager = $notificationManager;
    }

    /**
     * @param NotificationEvent $event
     */
    public function onNotificationCreated(NotificationEvent $event)
    {
        /** @var SubscriberNotification $notification */
        $notification = $this->em->merge($event->getNotification());

        // Enregistrement
        $this->em->persist($notification);
        $this->em->flush();

        // Envoi
        $this->notificationManager->send($notification);
    }
}
