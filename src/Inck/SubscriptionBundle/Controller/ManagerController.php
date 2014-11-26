<?php

namespace Inck\SubscriptionBundle\Controller;

use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;

/**
 * @Route("/subscription")
 */
class ManagerController extends Controller
{
    /**
     * @Route("/manager/{alias}/{page}",
     *     name="inck_subscription_manager_paginator",
     *     requirements={
     *         "alias" = "categories|tags|users",
     *         "page" = "\d+"
     *     },
     *     options={"expose"=true}
     * )
     * @Secure(roles="ROLE_USER")
     * @param string $alias
     * @param int $page
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function paginatorAction($alias, $page)
    {
        $paginator = $this->get('knp_paginator');
        $options = $this->getOptions($alias);

        $entities = $paginator->paginate(
            $this->getDoctrine()->getManager()->createQuery(sprintf(
                'SELECT e FROM %s e ORDER BY e.%s ASC',
                $options['entity'],
                $options['orderBy']
            )),
            $page,
            $options['limit']
        );

        return $this->render(sprintf(
            'InckSubscriptionBundle:Manager:%s.html.twig',
            $options['view']
        ), [
            'entities' => $entities,
        ]);
    }

    /**
     * @param string $alias
     * @return array|null
     */
    private function getOptions($alias)
    {
        switch ($alias) {
            case 'categories':
                return [
                    'entity'    => 'InckArticleBundle:Category',
                    'view'      => 'categories',
                    'orderBy'   => 'name',
                    'limit'     => 3,
                ];

            case 'tags':
                return [
                    'entity'    => 'InckArticleBundle:Tag',
                    'view'      => 'tags',
                    'orderBy'   => 'name',
                    'limit'     => 6,
                ];

            case 'users':
                return [
                    'entity'    => 'InckUserBundle:User',
                    'view'      => 'users',
                    'orderBy'   => 'username',
                    'limit'     => 2,
                ];

            default:
                return null;
        }
    }
}
