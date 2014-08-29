<?php

namespace Inck\NotifBundle\DependencyInjection;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\DependencyInjection\Loader;

/**
 * This is the class that loads and manages your bundle configuration
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html}
 */
class InckNotifExtension extends Extension
{
    /**
     * {@inheritDoc}
     */
    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $configs);

        $loader = new Loader\YamlFileLoader($container, new FileLocator(__DIR__.'/../Resources/config'));
        $loader->load('services.yml');
        $loader->load('parameters.yml');

        if (isset($config['user_class'])) {
            $container->setParameter('inck_notif.user_class', $config['user_class']);
        }
        if (isset($config['category_class'])) {
            $container->setParameter('inck_notif.category_class', $config['category_class']);
        }
        if (isset($config['tag_class'])) {
            $container->setParameter('inck_notif.tag_class', $config['tag_class']);
        }
    }
}
