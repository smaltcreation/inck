<?php

namespace Inck\RatchetBundle\Server;

use Doctrine\Common\Collections\ArrayCollection;
use FOS\UserBundle\Model\UserInterface;
use Inck\RatchetBundle\Entity\Client;
use Ratchet\ConnectionInterface;

class ClientManager
{
    /**
     * @var ArrayCollection
     */
    private $clients;

    public function __construct()
    {
        $this->clients = new ArrayCollection();
    }

    /**
     * @param ConnectionInterface $conn
     * @return Client
     */
    public function addConnection(ConnectionInterface $conn)
    {
        $this->clients[$conn->resourceId] = new Client($conn);

        return $this->clients[$conn->resourceId];
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function removeConnection(ConnectionInterface $conn)
    {
        $this->clients->remove($conn->resourceId);
    }

    /**
     * @param ConnectionInterface $conn
     */
    public function closeConnection(ConnectionInterface $conn)
    {
        $conn->close();
        $this->removeConnection($conn);
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
        /** @var Client $client */
        foreach($this->clients as $client)
        {
            if($client->getUser()->getUsername() === $user->getUsername()) {
                return $client;
            }
        }

        return null;
    }
}
