<?php

namespace Inck\NotificationBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Notification
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
     * @var UserInterface
     *
     * @ORM\ManyToOne(targetEntity="Symfony\Component\Security\Core\User\UserInterface")
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
    public function setCreatedAt($createdAt)
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
    public function setSentAt($sentAt)
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
    public function setDisplayedAt($displayedAt)
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
     * @param UserInterface $to
     * @return $this
     */
    public function setTo(UserInterface $to)
    {
        $this->to = $to;

        return $this;
    }

    /**
     * @return UserInterface
     */
    public function getTo()
    {
        return $this->to;
    }
}
