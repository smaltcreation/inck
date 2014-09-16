<?php

namespace Inck\NotifBundle\RPC;

use Doctrine\Common\Persistence\ObjectManager;
use Inck\NotifBundle\Model\SubscriptionInterface;
use Ratchet\ConnectionInterface;
use Symfony\Component\Security\Core\SecurityContext;

class SubscriptionService
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
     * @param ConnectionInterface $conn
     * @param $parameters
     * @internal param string $alias
     * @internal param int $entityId
     * @return bool
     */
    public function save(ConnectionInterface $conn, $parameters)
    {
        list($alias, $entityId) = $parameters;

        $user = $this->securityContext->getToken()->getUser();
        $class = $this->aliasToClass($alias);

        if(!$user || !$class) {
            return false;
        }

        $repository = $this->em->getRepository($class);
        $entity = $repository->find($entityId);

        if(!$entity) {
            return false;
        }

        $subscription = $repository->findOneBy(array(
            'subscriber'    => $user,
            'to'            => $entity,
        ));

        if($subscription) {
            $this->em->remove($subscription);
        }

        else {
            /** @var SubscriptionInterface $subscription */
            $subscription = new $class();

            $subscription->setSubscriber($user);
            $subscription->setTo($entity);

            $this->em->persist($subscription);
        }

        $this->em->flush();

        return true;
    }

    private function aliasToClass($alias)
    {
        return $this->parameters[$alias.'_class'];
    }
}