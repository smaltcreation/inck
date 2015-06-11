<?php

namespace Inck\UserBundle\Entity\Activity\Vote;

use Doctrine\ORM\Mapping as ORM;
use Inck\ArticleBundle\Entity\Vote;
use Inck\UserBundle\Entity\Activity;
use Inck\UserBundle\Entity\User;

/**
 * @ORM\Entity()
 */
class VoteActivity extends Activity {

    /**
     * @var string
     */
    protected $type = 'vote';

    /**
     * @ORM\OneToOne(targetEntity="Inck\ArticleBundle\Entity\Vote")
     * @ORM\JoinColumn(name="vote_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    protected $vote;

    /**
     * @param User $user
     * @param Vote $vote
     * @param null $title
     * @param null $content
     */
    function __construct(User $user, Vote $vote, $title = null, $content = null)
    {
        $this->title = $title;
        $this->content = $content;
        $this->createdAt = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $this->visibility = $vote->getUp() ? self::VISIBILITY_PUBLIC : self::VISIBILITY_CONTACT;
        $this->user = $user;
        $this->vote = $vote;
    }

    /**
     * Set vote
     *
     * @param \Inck\ArticleBundle\Entity\Vote $vote
     *
     * @return VoteActivity
     */
    public function setVote(\Inck\ArticleBundle\Entity\Vote $vote = null)
    {
        $this->vote = $vote;

        return $this;
    }

    /**
     * Get vote
     *
     * @return \Inck\ArticleBundle\Entity\Vote
     */
    public function getVote()
    {
        return $this->vote;
    }
}
