<?php

namespace Inck\NotificationBundle\Model;

use DateTime;
use Inck\UserBundle\Entity\User;

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
    public function setCreatedAt(DateTime $createdAt);

    /**
     * @return DateTime
     */
    public function getCreatedAt();

    /**
     * @param DateTime $sentAt
     * @return $this
     */
    public function setSentAt(DateTime $sentAt);

    /**
     * @return DateTime
     */
    public function getSentAt();

    /**
     * @param DateTime $displayedAt
     * @return $this
     */
    public function setDisplayedAt(DateTime $displayedAt);

    /**
     * @return DateTime
     */
    public function getDisplayedAt();

    /**
     * @param User $to
     * @return $this
     */
    public function setTo(User $to);

    /**
     * @return User
     */
    public function getTo();

    /**
     * @return string
     */
    public function getViewName();
}
