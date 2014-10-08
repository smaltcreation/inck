<?php

namespace Inck\NotificationBundle\RPC;

use Doctrine\Common\Persistence\ObjectManager;
use Inck\RatchetBundle\Entity\Client;

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
     * @param ObjectManager $em
     * @param array $parameters
     */
    public function __construct(ObjectManager $em, $parameters)
    {
        $this->em           = $em;
        $this->parameters   = $parameters;
    }

    /**
     * @param Client $client
     * @param array $parameters
     * @return bool
     */
    public function received(Client $client, array $parameters)
    {
        // TODO : enregistrer la date de réception de la notification
    }

    /**
     * @param Client $client
     * @param array $parameters
     * @return bool
     */
    public function read(Client $client, array $parameters)
    {
        // TODO : enregistrer la date de lecture de la notification
    }
}
