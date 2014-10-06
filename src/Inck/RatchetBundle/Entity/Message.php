<?php

namespace Inck\RatchetBundle\Entity;

class Message
{
    /**
     * @var string
     */
    private $method;

    /**
     * @var array
     */
    private $parameters = array();

    /**
     * @param string $method
     * @param array $parameters
     */
    public function __construct($method, $parameters = array())
    {
        $this->method       = $method;
        $this->parameters   = $parameters;
    }

    /**
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * @param string $method
     * @return $this
     */
    public function setMethod($method)
    {
        $this->method = $method;

        return $this;
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
     * @return $this
     */
    public function setParameters($parameters)
    {
        $this->parameters = $parameters;

        return $this;
    }

    /**
     * @param mixed $name
     * @param mixed $value
     */
    public function addParameter($name, $value)
    {
        $this->parameters[$name] = $value;
    }

    /**
     * @return string
     */
    public function __toString()
    {
        return json_encode(array(
            'method'        => $this->method,
            'parameters'    => $this->parameters,
        ));
    }
}
