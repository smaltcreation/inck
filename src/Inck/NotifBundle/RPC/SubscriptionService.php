<?php

namespace Inck\NotifBundle\RPC;

use Ratchet\ConnectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SubscriptionService
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param ConnectionInterface $conn
     * @param array $parameters
     * @return bool
     */
    public function save(ConnectionInterface $conn, $parameters)
    {
        list($entityName, $entityId) = $parameters;
        return 'test';
    }
}