<?php

namespace Inck\UserBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Inck\UserBundle\Entity\Activity;
use Inck\UserBundle\Entity\User;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

class ActivityController extends Controller
{

    public static function insert($activities)
    {
        if (!$activities) {
            throw new BadRequestHttpException();
        }

        $em = self::getDoctrine()->getManager();
        foreach ($activities as $activity) {
            $em->persist($activity);
        }
        $em->flush();


    }

    /**
     * @Template()
     * @param User $user
     */
    public function listAction(User $user)
    {
        /** @var Activity $activities */
        $activities = $this->getDoctrine()->getRepository('InckUserBundle:Activity')->findPrivateByUser($user);
        return array('activities' => $activities);

    }
}