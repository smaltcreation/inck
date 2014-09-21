<?php

namespace Inck\NotifBundle\Server;

use Doctrine\Common\Persistence\ObjectManager;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use SplObjectStorage;
use Symfony\Component\HttpFoundation\Session\Session;

class ServerService implements MessageComponentInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var ObjectManager $em
     */
    private $em;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var SplObjectStorage
     */
    private $clients;

    /**
     * @param ObjectManager $em
     * @param array $parameters
     * @param Session $session
     */
    public function __construct(Session $session, ObjectManager $em, $parameters)
    {
        $this->session          = $session;
        $this->em               = $em;
        $this->parameters       = $parameters;
        $this->clients          = new SplObjectStorage;
    }

    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    function onOpen(ConnectionInterface $conn)
    {
        $this->clients->attach($conn);
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).
     * SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn)
    {
        $this->clients->detach($conn);
    }

    /**
     * If there is an error with one of the sockets, or somewhere in the application where an Exception is thrown, the
     * Exception is sent back down the stack, handled by the Server and bubbled back up the application through this method
     * @param  ConnectionInterface $conn
     * @param  \Exception $e
     * @throws \Exception
     */
    function onError(ConnectionInterface $conn, \Exception $e)
    {
        echo "An error has occurred: {$e->getMessage()}\n";
        $conn->close();
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string $msg The message received
     * @throws \Exception
     */
    function onMessage(ConnectionInterface $from, $msg)
    {
        echo "received $msg from {$from->resourceId}\n";

        foreach ($this->clients as $client)
        {
            if ($from === $client)
            {
                $msg = json_decode($msg);

                $result = array(
                    'method'    => 'subscription.saved',
                    'options'   => array(
                        'id' => $msg->id,
                    ),
                );

                $result = json_encode($result);

                echo "send $result to {$from->resourceId}\n";
                $client->send($result);
            }
        }
    }
}