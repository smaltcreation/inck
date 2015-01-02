<?php

namespace Inck\NotificationBundle\RPC;

use DateTime;
use Inck\RatchetBundle\Doctrine\ORM\EntityManager;
use Inck\RatchetBundle\Entity\Client;
use Inck\RatchetBundle\Server\ClientManager;
use Inck\UserBundle\Entity\User;

class NotificationHandler
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var ClientManager
     */
    private $clientManager;

    /**
     * @param EntityManager $em
     * @param ClientManager $clientManager
     */
    public function __construct(EntityManager $em, $clientManager)
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
        $userRepository = $this->em->getRepository('InckUserBundle:User');
        $user = $userRepository->find($client->getUser()->getId());

        // Récupération de la notification
        $repository = $this->em->getRepository('InckNotificationBundle:SubscriberNotification');
        $notification = $repository->find($parameters['id']);

        // Vérification de la notification
        if (!$notification || $notification->getTo() !== $user || $notification->getDisplayedAt() !== null) {
            throw new \Exception('Invalid notification');
        }

        // Enregistrement de la date
        $notification->setDisplayedAt(DateTime::createFromFormat('d/m/Y H:i:s', $parameters['date']));
        $this->em->persist($notification);
        $this->em->flush();
    }
}
