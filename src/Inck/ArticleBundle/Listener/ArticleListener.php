<?php

namespace Inck\ArticleBundle\Listener;

use Inck\ArticleBundle\Event\ArticleEvent;
use Inck\NotificationBundle\Entity\ArticleNotification;
use Inck\NotificationBundle\Event\NotificationEvent;
use Inck\RatchetBundle\Doctrine\ORM\EntityManager;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ArticleListener
{
    /**
     * @var EntityManager
     */
    private $em;

	/**
	 * @var EventDispatcherInterface
	 */
	private $dispatcher;


	/**
	 * @param EntityManager $em
	 * @param EventDispatcherInterface $dispatcher
	 */
    public function __construct(EntityManager $em, EventDispatcherInterface $dispatcher)
    {
        $this->em           = $em;
        $this->dispatcher   = $dispatcher;
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
			$this->dispatcher->dispatch(
				'notification.create',
				new NotificationEvent(
					new ArticleNotification($article, $user)
				)
			);
		}
	}
}
