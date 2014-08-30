<?php

namespace Inck\NotifBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\JsonResponse;
use Inck\NotifBundle\Entity\Subscription;

class SubscriptionController extends Controller
{
    /**
     * @var $entity string
     * @var $id int
     * @Template()
     * @return array
     */
    public function buttonAction($entity, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $subscription = ($this->get('security.context')->isGranted('ROLE_USER'))
            ? $em->getRepository('InckNotifBundle:Subscription')->getByEntityAndUser($entity, $id, $this->getUser())
            : null;

        return array(
            'subscription' => $subscription,
            'entity' => $entity,
            'id' => $id
        );
    }

    public function newAction($entity, $id)
    {
        try
        {
            if(!$this->get('security.context')->getToken()->getUser())
            {
                throw new \Exception("Vous devez être connecté pour vous abonner.");
            }

            $em = $this->getDoctrine()->getManager();

            if($class = $this->container->getParameter($entity))
            {
                $user = $this->get('security.context')->getToken()->getUser();
                $object = $em->getRepository($class)->find($id);
                if(!$object)
                {
                    throw new \Exception("Entité inexistante.");
                }

                $subscription = $em->getRepository('InckNotifBundle:Subscription')->getByEntityAndUser($entity, $id, $user);
                if(!$subscription)
                {
                    $subscription = new Subscription();
                    $subscription->setSubscriber($this->container->get('security.context')->getToken()->getUser());
                    $subscription->setEntityName($entity);
                    $subscription->setEntityId($object->getId());
                    $em->persist($subscription);
                }
                else
                {
                    $em->remove($subscription);
                }
                $em->flush();

                return new JsonResponse(null, 204);
            }
            else
            {
                throw new \Exception($entity." n'est pas une entité correcte.");
            }
        }

        catch(\Exception $e)
        {
            return new JsonResponse(array(
                'modal'   => $this->renderView('InckNotifBundle:Subscription:error.html.twig', array(
                        'message'   => $e->getMessage(),
                    )),
            ), 400);
        }
    }
}
