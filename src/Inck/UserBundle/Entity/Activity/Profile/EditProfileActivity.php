<?php

namespace Inck\UserBundle\Entity\Activity\Profile;

use Doctrine\ORM\Mapping as ORM;
use Inck\ArticleBundle\Entity\Article;
use Inck\UserBundle\Entity\Activity;
use Inck\UserBundle\Entity\User;

/**
 * @ORM\Entity()
 */
class EditProfileActivity extends Activity {

    /**
     * @var string
     */
    protected $type = 'profil_edit';

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
        $this->visibility = self::VISIBILITY_CONTACT;
        $this->user = $user;
    }
}
