<?php

namespace Inck\NotificationBundle\Entity;

use Doctrine\ORM\Mapping as ORM;
use Inck\ArticleBundle\Entity\Article;
use Inck\UserBundle\Entity\User;
use Inck\NotificationBundle\Model\NotificationInterface;

/**
 * ArticleNotification
 *
 * @ORM\Table("article_notification")
 * @ORM\Entity()
 */
class ArticleNotification extends Notification implements NotificationInterface
{
    const VIEW_NAME = 'InckNotificationBundle:Notification:article.html.twig';

    /**
     * @var Article
     *
     * @ORM\ManyToOne(targetEntity="Inck\ArticleBundle\Entity\Article")
     */
    private $article;


    /**
     * @param Article $article
     * @param User $user
     */
    public function __construct(Article $article, User $user)
    {
        parent::__construct();

        $this->article      = $article;
        $this->to           = $user;
    }

	/**
	 * @return Article
	 */
	public function getArticle() {
		return $this->article;
	}

	/**
	 * @param Article $article
	 *
	 * @return $this
	 */
	public function setArticle(Article $article) {
		$this->article = $article;

		return $this;
	}

	/**
	 * @return string
	 */
	public function getViewName()
	{
		return self::VIEW_NAME;
	}
}
