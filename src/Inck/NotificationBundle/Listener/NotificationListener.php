<?php

namespace Inck\NotificationBundle\Listener;

use Inck\NotificationBundle\Event\NotificationEvent;
use Inck\NotificationBundle\Manager\NotificationManager;
use Inck\NotificationBundle\Model\NotificationInterface;
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
    public function onNotificationCreate(NotificationEvent $event)
    {
	    $notification = $event->getNotification();
	    $repository = $this->em->getRepository('InckNotificationBundle:Notification');

	    if ($repository->isAlreadySent($notification, new \DateInterval('P1D'))) {
            return;
        }

        // Enregistrement
	    /** @var NotificationInterface $notification */
	    $notification = $this->em->merge($event->getNotification());
        $this->em->persist($notification);
        $this->em->flush();

        // Envoi
        $this->notificationManager->send($notification);
    }
}
