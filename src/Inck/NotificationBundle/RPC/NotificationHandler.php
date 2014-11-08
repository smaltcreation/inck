<?php

namespace Inck\NotificationBundle\RPC;

use Doctrine\Common\Persistence\ObjectManager;
use Inck\NotificationBundle\Model\NotificationInterface;
use Inck\RatchetBundle\Entity\Client;
use Inck\RatchetBundle\Server\ClientManager;

class NotificationHandler
{
    /**
     * @var ObjectManager $em
     */
    private $em;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var ClientManager
     */
    private $clientManager;

    /**
     * @param ObjectManager $em
     * @param array $parameters
     * @param ClientManager $clientManager
     */
    public function __construct(ObjectManager $em, $parameters, $clientManager)
    {
        $this->em               = $em;
        $this->parameters       = $parameters;
        $this->clientManager    = $clientManager;
    }

    /**
     * @param Client $client
     * @param array $parameters
     * @return bool
     */
    public function displayed(Client $client, array $parameters)
    {
        // TODO : enregistrer la date d'affichage de la notification
    }
}
