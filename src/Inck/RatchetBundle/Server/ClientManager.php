<?php

namespace Inck\RatchetBundle\Server;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\UserInterface;
use Inck\RatchetBundle\Entity\Client;
use Monolog\Logger;
use Ratchet\ConnectionInterface;

class ClientManager
{
    /**
     * @var Logger $logger
     */
    private $logger;

    /**
     * @var ArrayCollection
     */
    private $clients;

    /**
     * @param Logger $logger
     */
    public function __construct(Logger $logger)
    {
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
    public function getClientByUser(UserInterface $user)
    {
        $this->logger->debug(sprintf(
            'looking for client %s, %d clients connected',
            $user->getUsername(),
            count($this->clients)
        ));

        /** @var Client $client */
        foreach($this->clients as $client)
        {
            if($client->getUser()->getUsername() === $user->getUsername()) {
                $this->logger->debug(sprintf(
                    'client found, id = %d',
                    $client->getId()
                ));

                return $client;
            }
        }

        $this->logger->debug('client not found');

        return null;
    }
}
