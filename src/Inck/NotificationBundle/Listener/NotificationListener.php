<?php

namespace Inck\NotificationBundle\Listener;

use Inck\NotificationBundle\Entity\SubscriberNotification;
use Inck\NotificationBundle\Event\NotificationEvent;
use Inck\NotificationBundle\Manager\NotificationManager;
use Inck\RatchetBundle\Doctrine\ORM\EntityManager;

class NotificationListener
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var NotificationManager
     */
    private $notificationManager;


    /**
     * @param EntityManager $em
     * @param NotificationManager $notificationManager
     */
    public function __construct(EntityManager $em, NotificationManager $notificationManager)
    {
        $this->em = $em;
        $this->notificationManager = $notificationManager;
    }

    /**
     * @param NotificationEvent $event
     */
    public function onNotificationCreated(NotificationEvent $event)
    {
        $repository = $this->em->getRepository('InckNotificationBundle:SubscriberNotification');

        /** @var SubscriberNotification $notification */
        $notification = $event->getNotification();

        if ($repository->isAlreadySent($notification, new \DateInterval('P1D'))) {
            return;
        }

        $notification = $this->em->merge($event->getNotification());

        // Enregistrement
        $this->em->persist($notification);
        $this->em->flush();

        // Envoi
        $this->notificationManager->send($notification);
    }
}
