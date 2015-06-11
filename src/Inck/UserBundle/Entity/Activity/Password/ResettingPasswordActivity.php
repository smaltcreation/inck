<?php

namespace Inck\UserBundle\Entity\Activity\Password;

use Doctrine\ORM\Mapping as ORM;
use Inck\ArticleBundle\Entity\Article;
use Inck\UserBundle\Entity\Activity;
use Inck\UserBundle\Entity\User;

/**
 * @ORM\Entity()
 */
class ResettingPasswordActivity extends Activity {

    /**
     * @var string
     */
    protected $type = 'password_resetting';

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
        $this->visibility = self::VISIBILITY_PRIVATE;
        $this->user = $user;
    }
}
