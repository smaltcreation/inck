<?php

namespace Inck\SubscriptionBundle\Exception;

use Exception;
use Inck\RatchetBundle\Entity\Client;

class InvalidRequestException extends Exception
{
    /**
     * @var Client
     */
    private $client;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @param Client $client
     * @param array $parameters
     * @param string $message
     */
    public function __construct(Client $client, array $parameters, $message)
    {
        $this->client = $client;
        $this->parameters = $parameters;
        $this->message = $message;
    }

    /**
     * @return Client
     */
    public function getClient()
    {
        return $this->client;
    }

    /**
     * @param Client $client
     */
    public function setClient($client)
    {
        $this->client = $client;
    }

    /**
     * @return array
     */
    public function getParameters()
    {
        return $this->parameters;
    }

    /**
     * @param array $parameters
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;
    }
}
