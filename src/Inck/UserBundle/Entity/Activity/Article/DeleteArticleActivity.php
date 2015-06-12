<?php

namespace Inck\UserBundle\Entity\Activity\Article;

use Doctrine\ORM\Mapping as ORM;
use Inck\ArticleBundle\Entity\Article;
use Inck\UserBundle\Entity\Activity;
use Inck\UserBundle\Entity\User;

/**
 * @ORM\Entity()
 */
class DeleteArticleActivity extends Activity {

    /**
     * @var string
     */
    protected $type = 'article_delete';

    /***
     * @var string
     *
     * @ORM\Column(name="article_title", type="string", length=255, nullable=true)
     */
    protected $articleTitle;
    

    /**
     * @param User $user
     * @param Article $article
     * @param null $title
     * @param null $content
     */
    function __construct(User $user, $articleTitle, $title = null, $content = null)
    {
        $this->title = $title;
        $this->content = $content;
        $this->createdAt = new \DateTime('now', new \DateTimeZone('Europe/Paris'));
        $this->visibility = self::VISIBILITY_PRIVATE;
        $this->user = $user;
        $this->articleTitle = $articleTitle;
    }

    /**
     * @return mixed
     */
    public function getArticleTitle()
    {
        return $this->articleTitle;
    }

    /**
     * @param mixed $articleTitle
     */
    public function setArticleTitle($articleTitle)
    {
        $this->articleTitle = $articleTitle;
    }


}
