<?php

namespace Inck\SubscriptionBundle\Model;

use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

interface SubscriptionInterface
{
    /**
     * @return integer
     */
    public function getId();

    /**
     * @param DateTime $createdAt
     */
    public function setCreatedAt($createdAt);

    /**
     * @return DateTime
     */
    public function getCreatedAt();

    /**
     * @param UserInterface $subscriber
     */
    public function setSubscriber(UserInterface $subscriber);

    /**
     * @return UserInterface
     */
    public function getSubscriber();

    /**
     * @param DateTime $readAt
     */
    public function setReadAt($readAt);

    /**
     * @return DateTime
     */
    public function getReadAt();

    /**
     * @param mixed $entity
     */
    public function setTo($entity);

    /**
     * @return mixed
     */
    public function getTo();
}
