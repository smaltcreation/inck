<?php

namespace Inck\SubscriptionBundle\Twig;

use Doctrine\ORM\EntityManager;
use Inck\SubscriptionBundle\Traits\SubscriptionTrait;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class SubscriptionExtension extends \Twig_Extension
{
    use SubscriptionTrait;

    /**
     * @var AuthorizationCheckerInterface $authorizationChecker
     */
    private $authorizationChecker;

    /**
     * @var TokenStorageInterface $tokenStorage
     */
    private $tokenStorage;

    /**
     * @var EntityManager $em
     */
    private $em;

    /**
     * @var \Twig_Environment $environment
     */
    private $environment;

    /**
     * @param AuthorizationCheckerInterface $authorizationChecker
     * @param EntityManager $em
     * @param array $parameters
     */
    public function __construct(AuthorizationCheckerInterface $authorizationChecker, TokenStorageInterface $tokenStorage, EntityManager $em, $parameters)
    {
        $this->authorizationChecker = $authorizationChecker;
        $this->tokenStorage         = $tokenStorage;
        $this->em                   = $em;
        $this->parameters           = $parameters;
    }

    /**
     * @param \Twig_Environment $environment
     */
    public function initRuntime(\Twig_Environment $environment)
    {
        $this->environment = $environment;
    }

    /**
     * @return string
     */
    public function getName()
    {
        return "subscribe";
    }

    /**
     * Returns a list of functions to add to the existing list.
     *
     * @return array An array of functions
     */
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
     * @param string $buttonClass
     * @return string
     */
    public function subscribeButton($alias, $entity, $buttonClass = null)
    {
        $subscribed = false;

        if($this->authorizationChecker->isGranted('ROLE_USER'))
        {
            $user   = $this->tokenStorage->getToken()->getUser();
            $class  = $this->aliasToClass($alias, true);

            $subscribed = $this
                ->em
                ->getRepository($class)
                ->findOneBy(array(
                    'subscriber'    => $user,
                    'to'            => $entity,
                ));
        }

        return $this->environment->render('InckSubscriptionBundle:Subscription:button.html.twig', array(
            'subscribed'    => $subscribed,
            'alias'         => $alias,
            'id'            => $entity->getId(),
            'buttonClass'   => $buttonClass,
        ));
    }
}
