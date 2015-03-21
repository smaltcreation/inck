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
     * @Route("/manager/history/{page}",
     *     name="inck_notification_manager_history",
     *     requirements={"page" = "\d+"},
     *     options={"expose"=true}
     * )
     * @Secure(roles="ROLE_USER")
     * @Template()
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function historyAction($page)
    {
        $repository = $this->getDoctrine()->getRepository('InckNotificationBundle:Notification');
        $manager = $this->get('inck_notification.manager.notification_manager');
        $paginator = $this->get('knp_paginator');
        $renders = array();

        $notifications = $paginator->paginate(
            $repository->getPaginatorQuery($this->getUser()),
            $page,
            5
        );

        foreach ($notifications as $notification) {
            $renders[] = $manager->render($notification);
        }

        return array(
            'notifications' => $renders,
        );
    }
}
