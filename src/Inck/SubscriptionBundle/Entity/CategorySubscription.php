<?php

namespace Inck\SubscriptionBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Inck\ArticleBundle\Entity\Category;
use Inck\SubscriptionBundle\Model\SubscriptionInterface;

/**
 * CategorySubscription
 *
 * @ORM\Table("category_subscription")
 * @ORM\Entity()
 */
class CategorySubscription extends Subscription implements SubscriptionInterface
{
    /**
     * @var Category
     *
     * @ORM\ManyToOne(targetEntity="Inck\ArticleBundle\Entity\Category")
     */
    private $to;

    /**
     * @return Category
     */
    public function getTo()
    {
        return $this->to;
    }

    /**
     * @param Category $entity
     * @return $this
     */
    public function setTo($entity)
    {
        $this->to = $entity;

        return $this;
    }
}
