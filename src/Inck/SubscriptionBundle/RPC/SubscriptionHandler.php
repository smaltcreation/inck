<?php

namespace Inck\SubscriptionBundle\RPC;

use Doctrine\Common\Persistence\ObjectManager;
use Inck\RatchetBundle\Entity\Client;
use Inck\SubscriptionBundle\Model\SubscriptionInterface;

class SubscriptionHandler
{
    /**
     * @var ObjectManager $em
     */
    private $em;

    /**
     * @var array
     */
    private $parameters;

    /**
     * @param ObjectManager $em
     * @param array $parameters
     */
    public function __construct(ObjectManager $em, $parameters)
    {
        $this->em           = $em;
        $this->parameters   = $parameters;
    }

    /**
     * @param Client $client
     * @param array $parameters
     * @return bool
     * @throws \Exception
     */
    public function save(Client $client, array $parameters)
    {
        // Vérification du client
        if(!$client) {
            throw new \Exception('Invalid client');
        }

        // Vérification des paramètres
        $requiredParameters = array(
            'entityAlias',
            'entityId',
            'id',
        );

        foreach($requiredParameters as $requiredParameter) {
            if (!isset($parameters[$requiredParameter])) {
                throw new \Exception(sprintf(
                    'Parameter "%s" is required',
                    $requiredParameter
                ));
            }
        }

        // Récupération de la classe
        $subscriptionClass  = $this->aliasToClass($parameters['entityAlias'], true);
        $class              = $this->aliasToClass($parameters['entityAlias']);

        if(!$subscriptionClass || !$class) {
            throw new \Exception(sprintf(
                'Alias "%s" invalid',
                $parameters['entityAlias']
            ));
        }

        // Récupération de l'entité
        $entity = $this
            ->em
            ->getRepository($class)
            ->find($parameters['entityId']);

        if(!$entity) {
            throw new \Exception(sprintf(
                'Entity "%d" invalid',
                $parameters['entityId']
            ));
        }

        $subscription = $this
            ->em
            ->getRepository($subscriptionClass)
            ->findOneBy(array(
                'subscriber'    => $client->getUser(),
                'to'            => $entity,
            ));

        if($subscription) {
            $this->em->remove($subscription);
        } else {
            /** @var SubscriptionInterface $subscription */
            $subscription = new $subscriptionClass();

            $subscription
                ->setSubscriber($client->getUser())
                ->setTo($entity);

            $this->em->persist($subscription);
        }

        $this->em->flush();

        return true;
    }

    /**
     * @param string $alias
     * @param bool $subscription
     * @return null
     */
    private function aliasToClass($alias, $subscription = false)
    {
        $key = sprintf(
            '%s%s_class',
            $alias,
            ($subscription) ? '_subscription' : ''
        );

        return isset($this->parameters[$key])
            ? $this->parameters[$key]
            : null;
    }
}
