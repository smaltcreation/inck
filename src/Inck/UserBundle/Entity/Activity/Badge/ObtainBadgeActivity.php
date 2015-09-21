<?php

namespace Inck\UserBundle\Entity\Activity\Badge;

use Doctrine\ORM\Mapping as ORM;
use Inck\UserBundle\Entity\Activity;
use Inck\UserBundle\Entity\User;
use Inck\UserBundle\Entity\Badge;

/**
 * @ORM\Entity()
 */
class ObtainBadgeActivity extends Activity {

    /**
     * @var string
     */
    protected $type = 'badge';

    /**
     * @ORM\OneToOne(targetEntity="Inck\UserBundle\Entity\Badge")
     * @ORM\JoinColumn(name="badge_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    protected $badge;

    /**
     * @param User $user
     * @param Badge $badge
     * @param null $title
     * @param null $content
     */
    function __construct(User $user, Badge $badge, $title = null, $content = null)
    {
        $this->title = $title;
        $this->content = $content;
        $this->createdAt = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $this->visibility = self::VISIBILITY_PUBLIC;
        $this->user = $user;
        $this->badge = $badge;
    }
}