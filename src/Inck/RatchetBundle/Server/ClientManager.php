<?php

namespace Inck\RatchetBundle\Server;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use Inck\RatchetBundle\Entity\Client;
use Monolog\Logger;
use Ratchet\ConnectionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class ClientManager
{
    /**
     * @var ObjectManager $em
     */
    private $em;

    /**
     * @var Logger $logger
     */
    private $logger;

    /**
     * @var ArrayCollection
     */
    private $clients;

    /**
     * @param ObjectManager $em
     * @param Logger $logger
     */
    public function __construct(ObjectManager $em, Logger $logger)
    {
        $this->em       = $em;
        $this->logger   = $logger;
        $this->clients  = new ArrayCollection();
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function addConnection(ConnectionInterface $conn)
    {
        $this->clients[$conn->resourceId] = new Client($conn);
        $this->logger->info('added connection #'.$conn->resourceId);
        $this->logTotalConnections();
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function removeConnection(ConnectionInterface $conn)
    {
        $this->clients->remove($conn->resourceId);
        $this->logger->info('removed connection #'.$conn->resourceId);
        $this->logTotalConnections();
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function closeConnection(ConnectionInterface $conn)
    {
        $conn->close();
        $this->logger->info('closed connection #'.$conn->resourceId);

        $this->removeConnection($conn);
    }

    private function logTotalConnections()
    {
        $this->logger->info('total connections = '.count($this->clients));
    }

    /**
     * @param ConnectionInterface $conn
     * @return Client|null
     */
    public function getClientByConnection(ConnectionInterface $conn)
    {
        return isset($this->clients[$conn->resourceId])
            ? $this->clients[$conn->resourceId]
            : null;
    }

    /**
     * @param UserInterface $user
     * @return Client|null
     */
    public function getClientByUser($user)
    {
        /** @var Client $client */
        foreach($this->clients as $client)
        {
            if($client->getUser() === $user) {
                return $client;
            }
        }

        return null;
    }
}