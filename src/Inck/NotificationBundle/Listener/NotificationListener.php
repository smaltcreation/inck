<?php

namespace Inck\NotificationBundle\Listener;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Inck\NotificationBundle\Entity\SubscriberNotification;
use Inck\NotificationBundle\Event\NotificationEvent;
use Inck\RatchetBundle\Server\ClientManager;
use Symfony\Bundle\TwigBundle\TwigEngine;

class NotificationListener
{
    /**
     * @var ObjectManager
     */
    private $em;

    /**
     * @var TwigEngine
     */
    private $templating;

    /**
     * @var ClientManager
     */
    private $clientManager;


    /**
     * @param ObjectManager $em
     * @param TwigEngine $templating
     * @param ClientManager $clientManager
     */
    public function __construct(ObjectManager $em, TwigEngine $templating, ClientManager $clientManager)
    {
        $this->em               = $em;
        $this->templating       = $templating;
        $this->clientManager    = $clientManager;
    }

    /**
     * @param NotificationEvent $event
     */
    public function onNotificationCreated(NotificationEvent $event)
    {
        /** @var SubscriberNotification $notification */
        $notification = $event->getNotification();

        // Enregistrement
        $this->em->persist($notification);
        $this->em->flush();

        // Envoi
        $client = $this->clientManager->getClientByUser($notification->getTo());

        if($client) {
            $client->sendMessage('notification.received', array(
                'id'    => $notification->getId(),
                'html'  => $this->templating->render(
                    $notification->getViewName(),
                    array(
                        'notification' => $notification,
                    )
                ),
            ));

            $notification->setSentAt(new DateTime());
        }

        // Mise à jour
        $this->em->persist($notification);
        $this->em->flush();
    }

    /**
     * @param NotificationEvent $event
     */
    public function onNotificationDisplayed(NotificationEvent $event)
    {

    }
}
