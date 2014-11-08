<?php

namespace Inck\NotificationBundle\Model;

use DateTime;
use Symfony\Component\Security\Core\User\UserInterface;

interface NotificationInterface
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
     * @param DateTime $sentAt
     * @return $this
     */
    public function setSentAt($sentAt);

    /**
     * @return DateTime
     */
    public function getSentAt();

    /**
     * @param DateTime $displayedAt
     * @return $this
     */
    public function setDisplayedAt($displayedAt);

    /**
     * @return DateTime
     */
    public function getDisplayedAt();

    /**
     * @param UserInterface $to
     * @return $this
     */
    public function setTo(UserInterface $to);

    /**
     * @return UserInterface
     */
    public function getTo();

    /**
     * @return string
     */
    public function getViewName();
}
