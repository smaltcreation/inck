<?php

namespace Inck\UserBundle\Entity\Activity\Article;

use Doctrine\ORM\Mapping as ORM;
use Inck\ArticleBundle\Entity\Article;
use Inck\UserBundle\Entity\Activity;
use Inck\UserBundle\Entity\User;

/**
 * @ORM\Entity()
 */
class NewArticleActivity extends Activity {

    /**
     * @var string
     */
    protected $type = 'article_new';

    /**
     * @ORM\OneToOne(targetEntity="Inck\ArticleBundle\Entity\Article")
     * @ORM\JoinColumn(name="article_id", referencedColumnName="id", onDelete="CASCADE")
     **/
    protected $article;

    /**
     * @param User $user
     * @param Article $article
     * @param null $title
     * @param null $content
     */
    function __construct(User $user, Article $article, $title = null, $content = null)
    {
        $this->title = $title;
        $this->content = $content;
        $this->createdAt = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $this->visibility = self::VISIBILITY_PRIVATE;
        $this->user = $user;
        $this->article = $article;
    }

    /**
     * Set article
     *
     * @param \Inck\ArticleBundle\Entity\Article $article
     *
     * @return NewArticleActivity
     */
    public function setArticle(\Inck\ArticleBundle\Entity\Article $article = null)
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
}
