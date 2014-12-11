<?php

use Symfony\Component\HttpKernel\Kernel;
use Symfony\Component\Config\Loader\LoaderInterface;

class AppKernel extends Kernel
{
    public function registerBundles()
    {
        $bundles = array(
            new Symfony\Bundle\FrameworkBundle\FrameworkBundle(),
            new Symfony\Bundle\SecurityBundle\SecurityBundle(),
            new Symfony\Bundle\TwigBundle\TwigBundle(),
            new Symfony\Bundle\MonologBundle\MonologBundle(),
            new Symfony\Bundle\SwiftmailerBundle\SwiftmailerBundle(),
            new Symfony\Bundle\AsseticBundle\AsseticBundle(),
            new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),
            new Sensio\Bundle\FrameworkExtraBundle\SensioFrameworkExtraBundle(),
            /* Extern bundles */
            new Braincrafted\Bundle\BootstrapBundle\BraincraftedBootstrapBundle(),
            new Knp\Bundle\MenuBundle\KnpMenuBundle(),
            new Knp\Bundle\PaginatorBundle\KnpPaginatorBundle(),
            new FOS\UserBundle\FOSUserBundle(),
            new JMS\AopBundle\JMSAopBundle(),
            new JMS\SecurityExtraBundle\JMSSecurityExtraBundle(),
            new JMS\DiExtraBundle\JMSDiExtraBundle($this),
            new Avocode\FormExtensionsBundle\AvocodeFormExtensionsBundle(),
            new Ornicar\GravatarBundle\OrnicarGravatarBundle(),
            new Ivory\CKEditorBundle\IvoryCKEditorBundle(),
            new Doctrine\Bundle\FixturesBundle\DoctrineFixturesBundle(),
            new Vich\UploaderBundle\VichUploaderBundle(),
            new FOS\JsRoutingBundle\FOSJsRoutingBundle(),
            new Avalanche\Bundle\ImagineBundle\AvalancheImagineBundle(),
            new Knp\Bundle\SnappyBundle\KnpSnappyBundle(),
            new Nelmio\JsLoggerBundle\NelmioJsLoggerBundle(),
            new Presta\SitemapBundle\PrestaSitemapBundle(),
            /* Our bundles */
            new Inck\UserBundle\InckUserBundle(),
            new Inck\CoreBundle\InckCoreBundle(),
            new Inck\ArticleBundle\InckArticleBundle(),
            new Inck\RatchetBundle\InckRatchetBundle(),
            new Inck\SubscriptionBundle\InckSubscriptionBundle(),
            new Inck\BidBundle\InckBidBundle(),
            new HWI\Bundle\OAuthBundle\HWIOAuthBundle(),
            new Inck\StatBundle\InckStatBundle(),
            new Inck\NotificationBundle\InckNotificationBundle(),
        );

        if (in_array($this->getEnvironment(), array('dev', 'test'))) {
            $bundles[] = new Symfony\Bundle\WebProfilerBundle\WebProfilerBundle();
            $bundles[] = new Sensio\Bundle\DistributionBundle\SensioDistributionBundle();
            $bundles[] = new Sensio\Bundle\GeneratorBundle\SensioGeneratorBundle();
            $bundles[] = new Elao\WebProfilerExtraBundle\WebProfilerExtraBundle();
        }

        return $bundles;
    }

    public function registerContainerConfiguration(LoaderInterface $loader)
    {
        $loader->load(__DIR__.'/config/config_'.$this->getEnvironment().'.yml');
    }
}
