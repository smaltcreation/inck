<?php

namespace Inck\SubscriptionBundle\Traits;


trait SubscriptionTrait
{
    /**
     * @var array
     */
    private $parameters;

    /**
     * @param string $alias
     * @param bool $subscription
     * @return null
     */
    private function aliasToClass($alias, $subscription = false)
    {
        $key = sprintf(
            '%s%s_class',
            $alias,
            ($subscription) ? '_subscription' : ''
        );

        return isset($this->parameters[$key])
            ? $this->parameters[$key]
            : null;
    }
}
