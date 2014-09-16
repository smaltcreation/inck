<?php

namespace Inck\NotifBundle\Twig;

use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\Security\Core\SecurityContext;

class SubscriptionExtension extends \Twig_Extension
{
    /**
     * @var SecurityContext $securityContext
     */
    private $securityContext;

    /**
     * @var ObjectManager $em
     */
    private $em;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @var \Twig_Environment $environment
     */
    private $environment;

    /**
     * @param SecurityContext $securityContext
     * @param ObjectManager $em
     * @param array $parameters
     */
    public function __construct(SecurityContext $securityContext, ObjectManager $em, $parameters)
    {
        $this->securityContext  = $securityContext;
        $this->em               = $em;
        $this->parameters       = $parameters;
    }

    /**
     * @param \Twig_Environment $environment
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
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
     * @param string $alias
     * @param mixed $entity
     * @return string
     */
    public function subscribeButton($alias, $entity)
    {
        $subscribed = false;

        if($this->securityContext->isGranted('ROLE_USER'))
        {
            $user   = $this->securityContext->getToken()->getUser();
            $class  = $this->aliasToClass($alias);

            $subscribed = $this
                ->em
                ->getRepository($class)
                ->findOneBy(array(
                    'subscriber'    => $user,
                    'to'            => $entity,
                ));
        }

        return $this->environment->render('InckNotifBundle:Subscription:button.html.twig', array(
            'subscribed'    => $subscribed,
            'alias'         => $alias,
            'id'            => $entity->getId(),
        ));
    }

    private function aliasToClass($alias)
    {
        return $this->parameters[$alias.'_class'];
    }
}