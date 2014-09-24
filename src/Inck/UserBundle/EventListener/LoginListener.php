<?php

namespace Inck\UserBundle\EventListener;

use FOS\UserBundle\FOSUserEvents;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\SecurityContext;

/**
 * Listener responsible to change the redirection at the end of the password resetting
 */
class LoginListener implements EventSubscriberInterface
{
    /**
     * @var Session
     */
    private $session;

    /**
     * @var SecurityContext
     */
    private $securityContext;

    /**
     * @param Session $session
     * @param SecurityContext $securityContext
     */
    public function __construct(Session $session, SecurityContext $securityContext)
    {
        $this->session          = $session;
        $this->securityContext  = $securityContext;
    }

    /**
     * {@inheritDoc}
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::SECURITY_IMPLICIT_LOGIN => 'setSession',
        );
    }

    public function setSession()
    {
        $this->session->set('user', $this->securityContext->getToken()->getUser());
    }
}