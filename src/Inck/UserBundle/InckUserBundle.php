<?php

namespace Inck\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class InckUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }
}
