<?php

namespace Inck\NotificationBundle\RPC;

use Doctrine\Common\Persistence\ObjectManager;
use Inck\RatchetBundle\Entity\Client;
use Inck\RatchetBundle\Server\ClientManager;
use Inck\UserBundle\Entity\User;
use \DateTime;

class NotificationHandler
{
    /**
     * @var ObjectManager $em
     */
    private $em;

    /**
     * @var ClientManager
     */
    private $clientManager;

    /**
     * @param ObjectManager $em
     * @param ClientManager $clientManager
     */
    public function __construct(ObjectManager $em, $clientManager)
    {
        $this->em               = $em;
        $this->clientManager    = $clientManager;
    }

    /**
     * @param Client $client
     * @param array $parameters
     * @throws \Exception
     */
    public function displayed(Client $client, array $parameters)
    {
        // Vérification du client
        if (!$client) {
            throw new \Exception('Invalid client');
        }

        // Vérification des paramètres
        $requiredParameters = array(
            'id',
            'date',
        );

        foreach ($requiredParameters as $requiredParameter) {
            if (!isset($parameters[$requiredParameter])) {
                throw new \Exception(sprintf(
                    'Parameter "%s" is required',
                    $requiredParameter
                ));
            }
        }

        // Récupération de l'utilisateur
        /** @var User $user */
        $user = $this->em->merge($client->getUser());

        // Récupération de la notification
        $repository = $this->em->getRepository('InckNotificationBundle:SubscriberNotification');
        $notification = $repository->find($parameters['id']);

        // Vérification de la notification
        if (!$notification || $notification->getTo() !== $user || $notification->getDisplayedAt() !== null) {
            throw new \Exception('Invalid notification');
        }

        // Enregistrement de la date
        $notification->setDisplayedAt(new DateTime($parameters['date']));
        $this->em->persist($notification);
        $this->em->flush();
    }
}
