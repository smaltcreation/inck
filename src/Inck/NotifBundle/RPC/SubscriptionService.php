<?php

namespace Inck\NotifBundle\RPC;

use Doctrine\Common\Persistence\ObjectManager;
use Inck\NotifBundle\Entity\SubscriptionRepository;
use Inck\NotifBundle\Model\SubscriptionInterface;
use Ratchet\ConnectionInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;

class SubscriptionService
{
    protected $container;

    public function __construct(ContainerInterface $container)
    {
        $this->container = $container;
    }

    /**
     * @param ConnectionInterface $conn
     * @param string $entityName
     * @param int $entityId
     * @return bool
     */
    public function save(ConnectionInterface $conn, $entityName, $entityId)
    {
        if(!$user = $this->container->get('security.context')->getToken()->getUser()) {
            return false;
        }

        if(!$class = $this->container->getParameter($entityName.'_class')) {
            return false;
        }

        /** @var ObjectManager $em */
        $em = $this->container->get('doctrine')->getManager();

        /** @var SubscriptionRepository $repository */
        $repository = $em->getRepository($class);

        if(!$entity = $repository->find($entityId)) {
            return false;
        }

        $subscription = $repository->findOneBy(array(
            'subscriber'    => $user,
            'to'            => $entity,
        ));

        if($subscription) {
            $em->remove($subscription);
        }

        else {
            /** @var SubscriptionInterface $subscription */
            $subscription = new $class();

            $subscription->setSubscriber($user);
            $subscription->setTo($entity);

            $em->persist($subscription);
        }

        $em->flush();

        return true;
    }
}