<?php

namespace Inck\SubscriptionBundle\Entity;

use DateTime;
use Doctrine\ORM\Mapping as ORM;
use Doctrine\ORM\Mapping\DiscriminatorColumn;
use Doctrine\ORM\Mapping\DiscriminatorMap;
use Doctrine\ORM\Mapping\Entity;
use Doctrine\ORM\Mapping\InheritanceType;
use Doctrine\ORM\Mapping\Table;
use Inck\UserBundle\Entity\User;
use Symfony\Component\Security\Core\User\UserInterface;

/**
 * Subscription
 *
 * @Table("subscription")
 * @Entity()
 * @InheritanceType("JOINED")
 * @DiscriminatorColumn(name="type", type="string")
 * @DiscriminatorMap({
 *     "user"       = "UserSubscription",
 *     "category"   = "CategorySubscription",
 *     "tag"        = "TagSubscription",
 * })
 */
class Subscription
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
     * @var DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    protected $createdAt;

    /**
     * @var User
     *
     * @ORM\ManyToOne(targetEntity="Symfony\Component\Security\Core\User\UserInterface", inversedBy="subscriptions")
     */
    protected $subscriber;


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
     * @param DateTime $createdAt
     * @return $this
     */
    public function setCreatedAt($createdAt)
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * Get createdAt
     *
     * @return DateTime
     */
    public function getCreatedAt()
    {
        return $this->createdAt;
    }

    /**
     * Set subscriber
     *
     * @param UserInterface $subscriber
     * @return $this
     */
    public function setSubscriber(UserInterface $subscriber = null)
    {
        $this->subscriber = $subscriber;

        return $this;
    }

    /**
     * Get subscriber
     *
     * @return UserInterface
     */
    public function getSubscriber()
    {
        return $this->subscriber;
    }
}
