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
        $this->logger->debug('added connection : '.$conn->resourceId);
        $this->logger->debug('user = '.print_r($conn->Session->get('user'), true));
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function removeConnection(ConnectionInterface $conn)
    {
        $this->clients->remove($conn->resourceId);
        $this->logger->debug('removed connection : '.$conn->resourceId);
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function closeConnection(ConnectionInterface $conn)
    {
        $conn->close();
        $this->logger->debug('closed connection : '.$conn->resourceId);

        $this->removeConnection($conn);
    }
}