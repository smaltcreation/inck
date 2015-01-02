<?php

namespace Inck\RatchetBundle\Server;

use Inck\NotificationBundle\Entity\SubscriberNotificationRepository;
use Inck\NotificationBundle\Manager\NotificationManager;
use Inck\NotificationBundle\Model\NotificationInterface;
use Inck\RatchetBundle\Doctrine\ORM\EntityManager;
use Ratchet\ConnectionInterface;
use Ratchet\MessageComponentInterface;
use Symfony\Bridge\Monolog\Logger;

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
     * @var EntityManager
     */
    private $em;

    /**
     * @var array
     */
    private $rpcHandlers;

    /**
     * @param ClientManager $clientManager
     * @param Logger $logger
     * @param EntityManager $em
     * @param NotificationManager $notificationManager
     * @internal param RegistryInterface $doctrine
     */
    public function __construct(ClientManager $clientManager, Logger $logger, EntityManager $em, NotificationManager $notificationManager)
    {
        $this->clientManager        = $clientManager;
        $this->logger               = $logger;
        $this->em                   = $em;
        $this->notificationManager  = $notificationManager;
        $this->rpcHandlers          = array();
    }

    /**
     * When a new connection is opened it will be passed to this method
     * @param  ConnectionInterface $conn The socket/connection that just connected to your application
     * @throws \Exception
     */
    function onOpen(ConnectionInterface $conn)
    {
        $client = $this->clientManager->addConnection($conn);

        // Envoi des nouvelles notifications
        /** @var SubscriberNotificationRepository $repository */
        $repository = $this->em->getRepository('InckNotificationBundle:SubscriberNotification');

        /** @var NotificationInterface $notification */
        foreach ($repository->getNew($client->getUser()) as $notification) {
            $this->notificationManager->send($notification);
        }
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
     */
    function onError(ConnectionInterface $conn, \Exception $e)
    {
        $this->logger->error(sprintf(
            '"%s" in file %s on line %d',
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
     */
    function onMessage(ConnectionInterface $from, $msg)
    {
        try {
            $this->logger->debug(sprintf(
                'received %s from %d',
                $msg,
                $from->resourceId
            ));

            $msg = json_decode($msg, true);

            if (!isset($msg['method']) || !isset($msg['parameters'])) {
                throw new \Exception('The received message is invalid');
            }

            list($alias, $method) = explode('.', $msg['method']);

            if (($handler = $this->getRPCHandler($alias)) === null) {
                throw new \Exception(sprintf(
                    'Alias "%s" invalid',
                    $alias
                ));
            }

            if (!method_exists($handler, $method)) {
                throw new \Exception(sprintf(
                    'Method "%s" invalid',
                    $method
                ));
            }

            $handler->$method(
                $this->clientManager->getClientByConnection($from),
                $msg['parameters']
            );
        } catch(\Exception $e) {
            $this->logger->error($e->getMessage());
        }
    }

    /**
     * @param mixed $rpcHandler
     * @param string $alias
     */
    public function addRPCHandler($rpcHandler, $alias)
    {
        $this->rpcHandlers[$alias] = $rpcHandler;

        $this->logger->debug(sprintf(
            'handler "%s" added',
            $alias
        ));
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
