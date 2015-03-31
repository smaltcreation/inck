<?php

namespace Inck\NotificationBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;
use Inck\UserBundle\Entity\User;

/**
 * Notification
 *
 * @ORM\Table("notification")
 * @Entity(repositoryClass="Inck\NotificationBundle\Entity\NotificationRepository")
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="type", type="string")
 * @DiscriminatorMap({
 *     "subscriber" = "SubscriberNotification",
 *     "article" = "ArticleNotification",
 * })
 */
class Notification
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    protected $createdAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="sentAt", type="datetime", nullable=true)
     */
    protected $sentAt;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="displayedAt", type="datetime", nullable=true)
     */
    protected $displayedAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Inck\UserBundle\Entity\User")
     */
    protected $to;


    public function __construct()
    {
        $this->createdAt = new DateTime();
    }

    /**
     * Get id
     *
     * @return integer 
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     * @return Notification
     */
    public function setCreatedAt(DateTime $createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return \DateTime 
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set sentAt
     *
     * @param \DateTime $sentAt
     * @return Notification
     */
    public function setSentAt(DateTime $sentAt)
    {
        $this->sentAt = $sentAt;

        return $this;
    }

    /**
     * Get sentAt
     *
     * @return \DateTime 
     */
    public function getSentAt()
    {
        return $this->sentAt;
    }

    /**
     * Set displayedAt
     *
     * @param \DateTime $displayedAt
     * @return Notification
     */
    public function setDisplayedAt(DateTime $displayedAt)
    {
        $this->displayedAt = $displayedAt;

        return $this;
    }

    /**
     * Get displayedAt
     *
     * @return \DateTime 
     */
    public function getDisplayedAt()
    {
        return $this->displayedAt;
    }

    /**
     * @param User $to
     * @return $this
     */
    public function setTo(User $to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @return User
     */
    public function getTo()
    {
        return $this->to;
    }
}
