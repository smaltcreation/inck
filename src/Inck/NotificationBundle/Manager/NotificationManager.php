<?php

namespace Inck\NotificationBundle\Manager;

use DateTime;
use Inck\NotificationBundle\Model\NotificationInterface;
use Inck\RatchetBundle\Doctrine\ORM\EntityManager;
use Inck\RatchetBundle\Server\ClientManager;
use Symfony\Bundle\TwigBundle\TwigEngine;

class NotificationManager
{
    /**
     * @var EntityManager
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
     * @param EntityManager $em
     * @param TwigEngine $templating
     * @param ClientManager $clientManager
     */
    public function __construct(EntityManager $em, TwigEngine $templating, ClientManager $clientManager)
    {
        $this->em               = $em;
        $this->templating       = $templating;
        $this->clientManager    = $clientManager;
    }

    /**
     * @param NotificationInterface $notification
     */
    public function send(NotificationInterface $notification)
    {
        if($client = $this->clientManager->getClientByUser($notification->getTo())) {
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

            // Mise à jour
            $this->em->persist($notification);
            $this->em->flush();
        }
    }
}
