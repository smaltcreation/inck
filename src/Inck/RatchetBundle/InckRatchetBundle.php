<?php

namespace Inck\RatchetBundle;

use Inck\RatchetBundle\DependencyInjection\Compiler\RPCCompilerPass;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class InckRatchetBundle extends Bundle
{
    public function build(ContainerBuilder $container)
    {
        parent::build($container);

        $container->addCompilerPass(new RPCCompilerPass());
    }
}
