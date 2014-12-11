<?php

namespace Inck\UserBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Inck\UserBundle\Entity\User;

class InckUserBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSUserBundle';
    }

    public function boot()
    {
        $router = $this->container->get('router');
        $event  = $this->container->get('event_dispatcher');

        //listen presta_sitemap.populate event
        $event->addListener(
            SitemapPopulateEvent::ON_SITEMAP_POPULATE,
            function(SitemapPopulateEvent $event) use ($router){

                $em = $this->container->get('doctrine')->getManager();

                $repository = $em->getRepository('InckUserBundle:User');
                $users = $repository->findByEnabled('true');

                if($users) {
                    foreach($users as $user) {
                        $url = $router->generate('fos_user_profile_preview', array(
                            'username' => $user->getUsername(),
                        ), true);

                        $this->addUrl($event, $url);
                    }
                }

            });
    }

    private function addUrl($event, $url)
    {
        $event->getGenerator()->addUrl(
            new UrlConcrete(
                $url,
                new \DateTime(),
                UrlConcrete::CHANGEFREQ_HOURLY,
                1
            ),
            'default'
        );
    }
}
