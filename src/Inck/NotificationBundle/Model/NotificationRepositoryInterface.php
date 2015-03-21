<?php

namespace Inck\NotificationBundle\Model;

use DateInterval;

interface NotificationRepositoryInterface
{
	/**
	 * @param NotificationInterface $notification
	 * @param DateInterval $interval
	 *
	 * @return bool
	 */
	public function isAlreadySent($notification, DateInterval $interval);
}
