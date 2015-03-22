<?php

namespace Inck\NotificationBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/notification")
 */
class ManagerController extends Controller
{
	/**
	 * @Route("/list/{page}", name="inck_notification_manager_list", requirements={"page" = "\d+"})
	 * @Secure(roles="ROLE_USER")
	 * @Template()
	 *
	 * @param int $page
	 * @param bool $popover
	 *
	 * @return \Symfony\Component\HttpFoundation\Response
	 * @internal param int $notificationsPerPage
	 *
	 */
    public function listAction($page, $popover)
    {
        $repository = $this->getDoctrine()->getRepository('InckNotificationBundle:Notification');
        $manager = $this->get('inck_notification.manager.notification_manager');
        $paginator = $this->get('knp_paginator');

        $notifications = $paginator->paginate(
            $repository->getPaginatorQuery($this->getUser()),
            $page,
	        $popover ? 3 : 6
        );

        foreach ($notifications as $notification) {
	        $notification->html = $manager->render($notification);
        }

        return array(
            'notifications' => $notifications,
	        'popover'       => $popover,
        );
    }

	/**
	 * @Route("/popover", name="inck_notification_manager_popover", options={"expose"=true})
	 * @Secure(roles="ROLE_USER")
	 * @Template()
	 */
	public function popoverAction()
	{
		return array();
	}

	/**
	 * @Route("/history/{page}", name="inck_notification_manager_history", defaults={"page" = "1"}, requirements={"page" = "\d+"})
	 * @Secure(roles="ROLE_USER")
	 * @Template()
	 *
	 * @param int $page
	 *
	 * @return array
	 */
	public function historyAction($page = 1)
	{
		return array(
			'page' => $page,
		);
	}
}
