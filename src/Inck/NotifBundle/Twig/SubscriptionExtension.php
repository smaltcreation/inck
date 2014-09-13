<?php

namespace Inck\NotifBundle\Twig;

use Symfony\Component\DependencyInjection\ContainerInterface;

class SubscriptionExtension extends \Twig_Extension
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    public function getName()
    {
        return "subscribe";
    }

    public function getFunctions()
    {
        return array(
            "subscribe_button" => new \Twig_Function_Method($this, 'subscribeButton', array('is_safe' => array('html')))
        );
    }

    public function subscribeButton($entityName, $entityId)
    {
        $subscribed = false;

        if($this->container->get('security.context')->isGranted('ROLE_USER'))
        {
            $em = $this->container->get('doctrine')->getManager();
            $user = $this->container->get('security.context')->getToken()->getUser();

            $subscribed = $em
                ->getRepository('InckNotifBundle:Subscription')
                ->getByEntityAndUser($entityName, $entityId, $user);
        }

        return $this->container->get('templating')->render('InckNotifBundle:Subscription:button.html.twig', array(
            'subscribed'    => $subscribed,
            'entityName'    => $entityName,
            'entityId'      => $entityId,
        ));
    }
}