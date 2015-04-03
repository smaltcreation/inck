<?php

namespace Inck\RatchetBundle\Server;

use Doctrine\Common\Collections\ArrayCollection;
use Inck\RatchetBundle\Entity\Client;
use Inck\UserBundle\Entity\User;
use Ratchet\ConnectionInterface;

class ClientManager
{
    /**
     * @var Client[]
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
     * @param User $user
     * @return Client|null
     */
    public function getClientByUser(User $user)
    {
        foreach ($this->clients as $client) {
            if ($client->getUser()->getId() === $user->getId()) {
                return $client;
            }
        }

        return null;
    }
}
