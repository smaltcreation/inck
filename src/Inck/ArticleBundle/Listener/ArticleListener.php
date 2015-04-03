<?php

namespace Inck\ArticleBundle\Listener;

use Inck\ArticleBundle\Event\ArticleEvent;
use Inck\NotificationBundle\Entity\ArticleNotification;
use Inck\NotificationBundle\Event\NotificationEvent;
use Inck\RatchetBundle\Doctrine\ORM\EntityManager;
use Inck\RatchetBundle\Entity\ServerMessage;
use Inck\RatchetBundle\Sender\MessageSender;

class ArticleListener
{
    /**
     * @var EntityManager
     */
    private $em;

    /**
     * @var MessageSender
     */
    private $sender;


	/**
	 * @param EntityManager $em
	 * @param MessageSender $sender
	 */
    public function __construct(EntityManager $em, MessageSender $sender)
    {
        $this->em = $em;
        $this->sender = $sender;
    }

	/**
	 * @param ArticleEvent $event
	 */
	public function onArticlePublish(ArticleEvent $event)
	{
		$article = $event->getArticle();
		$repository = $this->em->getRepository('InckUserBundle:User');
		$users = $repository->getUsersToNotifyByArticle($article);

		if (!$users || count($users) === 0) {
			return;
		}

		foreach ($users as $user) {
			$this->sender->send(new ServerMessage(
				'notification.create',
				new NotificationEvent(
					new ArticleNotification($article, $user)
				)
			));
		}
	}
}
