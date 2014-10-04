<?php

namespace Inck\SubscriptionBundle\RPC;

use Doctrine\Common\Persistence\ObjectManager;

class SubscriptionManager
{
    /**
     * @var ObjectManager $em
     */
    private $em;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @param ObjectManager $em
     * @param array $parameters
     */
    public function __construct(ObjectManager $em, $parameters)
    {
        $this->em           = $em;
        $this->parameters   = $parameters;
    }
}
