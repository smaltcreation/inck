<?php

namespace Inck\ArticleBundle\Event;

use Inck\ArticleBundle\Entity\Article;
use Symfony\Component\EventDispatcher\Event;

class ArticleEvent extends Event
{
    /**
     * @var Article
     */
    protected $article;

    /**
     * @param Article $article
     */
    public function __construct(Article $article)
    {
        $this->article = $article;
    }

    /**
     * @return Article
     */
    public function getArticle()
    {
        return $this->article;
    }
}
