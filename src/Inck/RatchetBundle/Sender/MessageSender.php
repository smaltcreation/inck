<?php

namespace Inck\RatchetBundle\Sender;

use Inck\RatchetBundle\Entity\ServerMessage;
use ZMQ;
use ZMQContext;
use ZMQSocket;

class MessageSender
{
	/**
	 * @var string
	 */
	private $address;

	/**
	 * @var string
	 */
	private $port;

	/**
	 * @var ZMQContext
	 */
	private $context;

	/**
	 * @var ZMQSocket
	 */
	private $socket;

	/**
	 * @var bool
	 */
	private $connected;


	/**
	 * @param string $address
	 * @param string $port
	 */
	public function __construct($address, $port)
	{
		$this->address      = $address;
		$this->port         = $port;
		$this->connected    = false;

		$this->context = new ZMQContext();
		$this->socket = $this->context->getSocket(ZMQ::SOCKET_PUSH);
	}

	/**
	 * @param ServerMessage $message
	 */
	public function send(ServerMessage $message)
	{
		if (!$this->connected) {
			$this->socket->connect(sprintf('tcp://%s:%s', $this->address, $this->port));
			$this->connected = true;
		}

		$this->socket->send(serialize($message));
	}
}
