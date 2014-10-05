<?php

namespace Inck\RatchetBundle\DependencyInjection\Compiler;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\Reference;

class RPCCompilerPass implements CompilerPassInterface
{
    public function process(ContainerBuilder $container)
    {
        $serviceName = 'inck_ratchet.server.server_service';

        if (!$container->hasDefinition($serviceName)) {
            return;
        }

        $definition = $container->getDefinition($serviceName);
        $taggedServices = $container->findTaggedServiceIds('inck_ratchet.rpc');

        foreach ($taggedServices as $id => $tagAttributes) {
            foreach ($tagAttributes as $attributes) {
                $definition->addMethodCall(
                    'addRPCHandler',
                    array(new Reference($id), $attributes['alias'])
                );
            }
        }
    }
}
