<?php

namespace Inck\NotifBundle\Twig;

use Doctrine\Common\Persistence\ObjectManager;
use Inck\NotifBundle\Entity\SubscriptionRepository;
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
            "subscribe_button" => new \Twig_Function_Method($this, 'subscribeButton', array(
                'is_safe' => array('html'),
            )
        ));
    }

    /**
     * @param $entityName
     * @param $entityId
     * @return string
     */
    public function subscribeButton($entityName, $entityId)
    {
        $subscribed = false;

        if($this->container->get('security.context')->isGranted('ROLE_USER'))
        {
            $class = $this->container->getParameter($entityName.'_class');

            /** @var ObjectManager $em */
            $em = $this->container->get('doctrine')->getManager();

            /** @var SubscriptionRepository $repository */
            $repository = $em->getRepository($class);

            $entity = $repository->find($entityId);
            $user = $this->container->get('security.context')->getToken()->getUser();

            $subscribed = $em
                ->getRepository($class)
                ->findOneBy(array(
                    'subscriber'    => $user,
                    'to'            => $entity,
                ));
        }

        return $this->container->get('templating')->render('InckNotifBundle:Subscription:button.html.twig', array(
            'subscribed'    => $subscribed,
            'entityName'    => $entityName,
            'entityId'      => $entityId,
        ));
    }
}