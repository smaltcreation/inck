<?php

namespace Inck\UserBundle\Entity\Activity\Subscription;

use Doctrine\ORM\Mapping as ORM;
use Inck\ArticleBundle\Entity\Article;
use Inck\UserBundle\Entity\Activity;
use Inck\UserBundle\Entity\User;

/**
 * @ORM\Entity()
 */
class NewSubscriptionActivity extends Activity {

    /**
     * @var string
     */
    protected $type = 'subscription_new';

    /**
     * @param User $user
     * @param null $title
     * @param null $content
     */
    function __construct(User $user, $title = null, $content = null)
    {
        $this->title = $title;
        $this->content = $content;
        $this->createdAt = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $this->visibility = self::VISIBILITY_PUBLIC;
        $this->user = $user;
    }
}
