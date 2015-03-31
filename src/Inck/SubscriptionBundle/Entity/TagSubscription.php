<?php

namespace Inck\SubscriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Inck\ArticleBundle\Entity\Tag;
use Inck\SubscriptionBundle\Model\SubscriptionInterface;

/**
 * TagSubscription
 *
 * @ORM\Table("tag_subscription")
 * @ORM\Entity()
 */
class TagSubscription extends Subscription implements SubscriptionInterface
{
    /**
     * @var Tag
     *
     * @ORM\ManyToOne(targetEntity="Inck\ArticleBundle\Entity\Tag")
     */
    private $to;

    /**
     * @return Tag
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param Tag $entity
     * @return $this
     */
    public function setTo($entity)
    {
        $this->to = $entity;

        return $this;
    }
}
