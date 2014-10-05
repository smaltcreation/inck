<?php

namespace Inck\RatchetBundle\Server;

use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Bridge\Monolog\Logger;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class Server implements MessageComponentInterface
{
    /**
     * @var ClientManager $clientManager
     */
    private $clientManager;

    /**
     * @var Logger $logger
     */
    private $logger;

    /**
     * @var array
     */
    private $rpcHandlers;

    /**
     * @param ClientManager $clientManager
     * @param Logger $logger
     */
    public function __construct(ClientManager $clientManager, Logger $logger)
    {
        $this->clientManager    = $clientManager;
        $this->logger           = $logger;
        $this->rpcHandlers      = array();
    }

    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    function onOpen(ConnectionInterface $conn)
    {
        $this->clientManager->addConnection($conn);
    }

    /**
     * This is called before or after a socket is closed (depends on how it's closed).
     * SendMessage to $conn will not result in an error if it has already been closed.
     * @param  ConnectionInterface $conn The socket/connection that is closing/closed
     * @throws \Exception
     */
    function onClose(ConnectionInterface $conn)
    {
        $this->clientManager->removeConnection($conn);
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
        $this->logger->error(sprintf(
            '"%s" in file %s at line %d',
            $e->getMessage(),
            $e->getFile(),
            $e->getLine()
        ));

        $this->clientManager->closeConnection($conn);
    }

    /**
     * Triggered when a client sends data through the socket
     * @param  \Ratchet\ConnectionInterface $from The socket/connection that sent the message to your application
     * @param  string $msg The message received
     * @return bool
     * @throws \Exception
     */
    function onMessage(ConnectionInterface $from, $msg)
    {
        $this->logger->debug(sprintf(
           'received %s from %d',
           $msg,
           $from->resourceId
        ));

        $msg = json_decode($msg, true);

        if (!isset($msg['method']) || !isset($msg['parameters'])) {
            $this->logger->error('The received message is invalid');
            return false;
        }

        list($alias, $method) = explode('.', $msg['method']);

        if(($handler = $this->getRPCHandler($alias)) === null) {
            $this->logger->error(sprintf(
                'Alias "%s" invalid',
                $alias
            ));

            return false;
        }

        if(!method_exists($handler, $method)) {
            $this->logger->error(sprintf(
                'Method "%s" invalid',
                $method
            ));
            return false;
        }

        return $handler->$method($msg['parameters']);
    }

    /**
     * @param mixed $rpcHandler
     * @param string $alias
     */
    public function addRPCHandler($rpcHandler, $alias)
    {
        $this->logger->debug('handler '.$alias.' added');
        $this->rpcHandlers[$alias] = $rpcHandler;
    }

    /**
     * @param string $alias
     * @return mixed
     */
    public function getRPCHandler($alias)
    {
        if (array_key_exists($alias, $this->rpcHandlers)) {
            return $this->rpcHandlers[$alias];
        }

        return null;
    }
}
