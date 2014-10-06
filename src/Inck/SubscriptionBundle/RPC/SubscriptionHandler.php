<?php

namespace Inck\SubscriptionBundle\RPC;

use Doctrine\Common\Persistence\ObjectManager;
use Inck\RatchetBundle\Entity\Client;
use Inck\SubscriptionBundle\Model\SubscriptionInterface;
use Symfony\Component\Security\Core\User\UserInterface;

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
     */
    public function save(Client $client, array $parameters)
    {
        try {
            // Vérification du client
            if (!$client) {
                throw new \Exception('Invalid client');
            }

            // Vérification des paramètres
            $requiredParameters = array(
                'entityAlias',
                'entityId',
                'id',
            );

            foreach ($requiredParameters as $requiredParameter) {
                if (!isset($parameters[$requiredParameter])) {
                    throw new \Exception(sprintf(
                        'Parameter "%s" is required',
                        $requiredParameter
                    ));
                }
            }

            // Récupération de l'utilisateur
            /** @var UserInterface $user */
            $user = $this->em->merge($client->getUser());

            // Récupération de la classe
            $subscriptionClass = $this->aliasToClass($parameters['entityAlias'], true);
            $class = $this->aliasToClass($parameters['entityAlias']);

            if (!$subscriptionClass || !$class) {
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

            if (!$entity) {
                throw new \Exception(sprintf(
                    'Entity "%d" invalid',
                    $parameters['entityId']
                ));
            }

            $subscription = $this
                ->em
                ->getRepository($subscriptionClass)
                ->findOneBy(array(
                    'subscriber' => $user,
                    'to' => $entity,
                ));

            if ($subscription) {
                $this->em->remove($subscription);
            } else {
                /** @var SubscriptionInterface $subscription */
                $subscription = new $subscriptionClass();

                $subscription
                    ->setSubscriber($user)
                    ->setTo($entity);

                $this->em->persist($subscription);
            }

            $this->em->flush();

            $client->getConnection()->send(json_encode(array(
                'method'        => 'subscription.saved',
                'parameters'    => array(
                    'id' => $parameters['id'],
                ),
            )));
        } catch(\Exception $e) {
            $client->getConnection()->send(json_encode(array(
                'method'        => 'subscription.error',
                'parameters'    => array(
                    'code'          => $e->getCode(),
                    'message'       => $e->getMessage(),
                ),
            )));
        }
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
