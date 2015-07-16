<?php

namespace Inck\UserBundle\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Activity
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Inck\UserBundle\Entity\ActivityRepository")
 * @ORM\InheritanceType("SINGLE_TABLE")
 * @ORM\DiscriminatorColumn(name="type", type="string")
 * @ORM\DiscriminatorMap({
    * "default"             = "Inck\UserBundle\Entity\Activity",
    * "article_delete"      = "Inck\UserBundle\Entity\Activity\Article\DeleteArticleActivity",
    * "article_publish"     = "Inck\UserBundle\Entity\Activity\Article\PublishArticleActivity",
    * "password_change"     = "Inck\UserBundle\Entity\Activity\Password\ChangePasswordActivity",
    * "password_resetting"  = "Inck\UserBundle\Entity\Activity\Password\ResettingPasswordActivity",
    * "profile_edit"        = "Inck\UserBundle\Entity\Activity\Profile\EditProfileActivity",
    * "subscription_new"    = "Inck\UserBundle\Entity\Activity\Subscription\NewSubscriptionActivity",
    * "vote"                = "Inck\UserBundle\Entity\Activity\Vote\VoteActivity"
 * })
 */
class Activity
{
    const VISIBILITY_HIDDEN     = 0;
    const VISIBILITY_PUBLIC     = 1;
    const VISIBILITY_PRIVATE    = 2;
    const VISIBILITY_CONTACT    = 3;

    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * @var string
     */
    protected $type = 'default';

    /**
     * @var string
     *
     * @ORM\Column(name="title", type="string", length=255, nullable=true)
     */
    protected $title;

    /**
     * @var string
     *
     * @ORM\Column(name="content", type="text", nullable=true)
     */
    protected $content;

    /**
     * @var \DateTime
     *
     * @ORM\Column(name="createdAt", type="datetime")
     */
    protected $createdAt;

    /**
     * @var integer
     *
     * @ORM\Column(name="visibility", type="integer")
     */
    protected $visibility;

    /**
     * @ORM\ManyToOne(targetEntity="Inck\UserBundle\Entity\User", inversedBy="activities")
     * @ORM\JoinColumn(nullable=false, onDelete="CASCADE")
     */
    protected $user;


    function __construct(User $user, $visibility, $title = null, $content = null)
    {
        $this->title = $title;
        $this->content = $content;
        $this->createdAt = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $this->visibility = $visibility;
        $this->user = $user;
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
     * Set title
     *
     * @param string $title
     *
     * @return Activity
     */
    public function setTitle($title)
    {
        $this->title = $title;

        return $this;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getTitle()
    {
        return $this->title;
    }

    /**
     * Get title
     *
     * @return string
     */
    public function getType()
    {
        return $this->type;
    }

    /**
     * Set content
     *
     * @param string $content
     *
     * @return Activity
     */
    public function setContent($content)
    {
        $this->content = $content;

        return $this;
    }

    /**
     * Get content
     *
     * @return string
     */
    public function getContent()
    {
        return $this->content;
    }

    /**
     * Set createdAt
     *
     * @param \DateTime $createdAt
     *
     * @return Activity
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
     * Set visibility
     *
     * @param integer $visibility
     *
     * @return Activity
     */
    public function setVisibility($visibility)
    {
        $this->visibility = $visibility;

        return $this;
    }

    /**
     * Get visibility
     *
     * @return integer
     */
    public function getVisibility()
    {
        return $this->visibility;
    }

    /**
     * Set user
     *
     * @param \Inck\UserBundle\Entity\User $user
     *
     * @return Activity
     */
    public function setUser(\Inck\UserBundle\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user
     *
     * @return \Inck\UserBundle\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }
}
