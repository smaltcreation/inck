<?php

namespace Inck\RatchetBundle\Server;

use Doctrine\Common\Persistence\ObjectManager;
use Doctrine\Common\Collections\ArrayCollection;
use Inck\RatchetBundle\Entity\Client;
use Monolog\Logger;
use Ratchet\ConnectionInterface;

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
}