<?php

namespace Inck\SubscriptionBundle\RPC;

use Doctrine\Common\Persistence\ObjectManager;
use Inck\ArticleBundle\Entity\Category;
use Inck\ArticleBundle\Entity\Tag;
use Inck\NotificationBundle\Entity\SubscriberNotification;
use Inck\NotificationBundle\Event\NotificationEvent;
use Inck\NotificationBundle\InckNotificationsEvents;
use Inck\RatchetBundle\Entity\Client;
use Inck\SubscriptionBundle\Model\SubscriptionInterface;
use Inck\SubscriptionBundle\Traits\SubscriptionTrait;
use Inck\UserBundle\Entity\User;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\Security\Core\User\UserInterface;

class SubscriptionHandler
{
    use SubscriptionTrait;

    /**
     * @var ObjectManager $em
     */
    private $em;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param ObjectManager $em
     * @param EventDispatcherInterface $dispatcher
     * @param array $parameters
     */
    public function __construct(ObjectManager $em, EventDispatcherInterface $dispatcher, array $parameters)
    {
        $this->em           = $em;
        $this->dispatcher   = $dispatcher;
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
            /** @var User|Category|Tag $entity */
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

            // Recherche de l'abonnement
            $subscription = $this
                ->em
                ->getRepository($subscriptionClass)
                ->findOneBy(array(
                    'subscriber'    => $user,
                    'to'            => $entity,
                ));

            // Suppression de l'abonnement
            if ($subscription) {
                $this->em->remove($subscription);
            }

            // Création de l'abonnement
            else {
                /** @var SubscriptionInterface $subscription */
                $subscription = new $subscriptionClass();

                $subscription
                    ->setSubscriber($user)
                    ->setTo($entity);

                $this->em->persist($subscription);

                // Création d'une notification
                if ($parameters['entityAlias'] === 'user') {
                    $this->dispatcher->dispatch(
                        // TODO: use class InckNotificationsEvents
                        //InckNotificationsEvents::NOTIFICATION_CREATED,
                        'notification.created',
                        new NotificationEvent(
                            new SubscriberNotification($user, $entity)
                        )
                    );
                }
            }

            // Enregistrement
            $this->em->flush();

            $client->sendMessage('subscription.saved', [
                'id' => $parameters['id'],
            ]);
        } catch(\Exception $e) {
            $client->sendMessage('subscription.error', [
                'id'        => $parameters['id'],
                'code'      => $e->getCode(),
                'message'   => $e->getMessage(),
            ]);
        }
    }
}
