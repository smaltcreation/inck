<?php

namespace Inck\RatchetBundle\Entity;

use Ratchet\ConnectionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class Client
{
    /**
     * @var ConnectionInterface $connection
     */
    private $connection;

    /**
     * @var UserInterface
     */
    private $user;


    /**
     * @param ConnectionInterface $connection
     * @param UserInterface $user
     */
    public function __construct(ConnectionInterface $connection, UserInterface $user = null)
    {
        $this->connection   = $connection;
        $this->user         = $user;
    }

    /**
     * @return ConnectionInterface
     */
    public function getConnection()
    {
        return $this->connection;
    }

    /**
     * @param ConnectionInterface $connection
     * @return Client
     */
    public function setConnection($connection)
    {
        $this->connection = $connection;
        return $this;
    }

    /**
     * @return UserInterface
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * @param UserInterface $user
     * @return Client
     */
    public function setUser($user)
    {
        $this->user = $user;
        return $this;
    }
}
