<?php

namespace Inck\UserBundle\EventListener;

use FOS\UserBundle\Event\UserEvent;
use FOS\UserBundle\FOSUserEvents;
use FOS\UserBundle\Model\UserManagerInterface;
use Monolog\Logger;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Security\Core\SecurityContext;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Http\Event\InteractiveLoginEvent;
use Symfony\Component\Security\Http\SecurityEvents;

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
     * @var Logger
     */
    private $logger;

    /**
     * @var UserManagerInterface
     */
    private $userManager;

    /**
     * @param Session $session
     * @param SecurityContext $securityContext
     * @param Logger $logger
     */
    public function __construct(Session $session, SecurityContext $securityContext, Logger $logger, UserManagerInterface $userManager)
    {
        $this->session          = $session;
        $this->securityContext  = $securityContext;
        $this->logger           = $logger;
        $this->userManager      = $userManager;
    }

    /**
     * @return array
     */
    public static function getSubscribedEvents()
    {
        return array(
            FOSUserEvents::SECURITY_IMPLICIT_LOGIN => 'onImplicitLogin',
            SecurityEvents::INTERACTIVE_LOGIN => 'onSecurityInteractiveLogin',
        );
    }

    /**
     * @param UserEvent $event
     */
    public function onImplicitLogin(UserEvent $event)
    {
        $this->setSession($event->getUser());
    }

    /**
     * @param InteractiveLoginEvent $event
     */
    public function onSecurityInteractiveLogin(InteractiveLoginEvent $event)
    {
        $this->setSession($event->getAuthenticationToken()->getUser());
    }

    /**
     * @param UserInterface $user
     */
    private function setSession(UserInterface $user)
    {
        $user = $this->userManager->findUserByUsername($user->getUsername());
        $this->session->set('user', $user);
    }
}