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
     * @return $this
     */
    public function setCreatedAt($createdAt);

    /**
     * @return DateTime
     */
    public function getCreatedAt();

    /**
     * @param UserInterface $subscriber
     * @return $this
     */
    public function setSubscriber(UserInterface $subscriber);

    /**
     * @return UserInterface
     */
    public function getSubscriber();

    /**
     * @param mixed $entity
     * @return $this
     */
    public function setTo($entity);

    /**
     * @return mixed
     */
    public function getTo();
}
