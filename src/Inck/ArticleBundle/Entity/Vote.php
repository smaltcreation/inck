<?php

namespace Inck\ArticleBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

/**
 * Vote
 *
 * @ORM\Table()
 * @ORM\Entity(repositoryClass="Inck\ArticleBundle\Entity\VoteRepository")
 */
class Vote
{
    /**
     * @var integer
     *
     * @ORM\Column(name="id", type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue(strategy="AUTO")
     */
    private $id;

    /**
     * @var boolean
     *
     * @ORM\Column(name="up", type="boolean")
     */
    private $up;

    /**
     * @var \DateTime
     *
     * @Assert\DateTime()
     * @ORM\Column(name="submittedOn", type="datetime")
     */
    private $submittedOn;

    /**
     * @ORM\ManyToOne(targetEntity="Inck\ArticleBundle\Entity\Article", inversedBy="votes")
     * @ORM\JoinColumn(nullable=false)
     */
    private $article;

    /**
     * @ORM\ManyToOne(targetEntity="Inck\UserBundle\Entity\User", inversedBy="votes")
     * @ORM\JoinColumn(nullable=true)
     */
    private $user;


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
     * Set up
     *
     * @param boolean $up
     * @return Vote
     */
    public function setUp($up)
    {
        $this->up = $up;

        return $this;
    }

    /**
     * Get up
     *
     * @return boolean 
     */
    public function getUp()
    {
        return $this->up;
    }

    /**
     * Set submittedOn
     *
     * @param \DateTime $submittedOn
     * @return Vote
     */
    public function setSubmittedOn($submittedOn)
    {
        $this->submittedOn = $submittedOn;

        return $this;
    }

    /**
     * Get submittedOn
     *
     * @return \DateTime 
     */
    public function getSubmittedOn()
    {
        return $this->submittedOn;
    }

    /**
     * Set article
     *
     * @param \Inck\ArticleBundle\Entity\Article $article
     * @return Vote
     */
    public function setArticle(\Inck\ArticleBundle\Entity\Article $article)
    {
        $this->article = $article;

        return $this;
    }

    /**
     * Get article
     *
     * @return \Inck\ArticleBundle\Entity\Article 
     */
    public function getArticle()
    {
        return $this->article;
    }


    /**
     * Set user
     *
     * @param \Inck\UserBundle\Entity\User $user
     * @return Vote
     */
    public function setUser(\Inck\UserBundle\Entity\User $user = null)
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
