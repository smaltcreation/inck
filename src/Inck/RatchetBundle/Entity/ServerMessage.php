<?php

namespace Inck\RatchetBundle\Entity;

use Symfony\Component\EventDispatcher\Event;

class ServerMessage
{
	/**
	 * @var string
	 */
	private $name;

	/**
	 * @var Event
	 */
	private $event;

	/**
	 * @param string $name
	 * @param Event $event
	 */
	public function __construct($name, $event)
	{
		$this->name = $name;
		$this->event = $event;
	}

	/**
	 * @return string
	 */
	public function getName() {
		return $this->name;
	}

	/**
	 * @param string $name
	 */
	public function setName($name) {
		$this->name = $name;
	}

	/**
	 * @return Event
	 */
	public function getEvent() {
		return $this->event;
	}

	/**
	 * @param Event $event
	 */
	public function setEvent($event) {
		$this->event = $event;
	}
}
