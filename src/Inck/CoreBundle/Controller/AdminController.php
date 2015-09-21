<?php

namespace Inck\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Inck\ArticleBundle\Entity\ArticleRepository;
use Inck\UserBundle\Entity\UserRepository;

/**
 * @Route("/admin")
 */
class AdminController extends Controller
{
    /**
     * @Route("/", name="inck_core_admin_index")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/{alias}/{page}",
     *     name="inck_core_admin_paginator",
     *     requirements={
     *         "alias" = "articles|categories|users|badges",
     *         "page" = "\d+"
     *     },
     *     options={"expose"=true}
     * )
     * @Secure(roles="ROLE_ADMIN")
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
                'SELECT e FROM %s e ORDER BY e.%s DESC',
                $options['entity'],
                $options['orderBy']
            )),
            $page,
            $options['limit']
        );

        return $this->render(sprintf(
            'InckCoreBundle:Admin:%s.html.twig',
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
            case 'articles':
                return [
                    'entity'    => 'InckArticleBundle:Article',
                    'view'      => 'articles',
                    'orderBy'   => 'updatedAt',
                    'limit'     => 5,
                ];

            case 'categories':
                return [
                    'entity'    => 'InckArticleBundle:Category',
                    'view'      => 'categories',
                    'orderBy'   => 'id',
                    'limit'     => 5,
                ];

            case 'users':
                return [
                    'entity'    => 'InckUserBundle:User',
                    'view'      => 'users',
                    'orderBy'   => 'id',
                    'limit'     => 5,
                ];

            case 'badges':
                return [
                    'entity'    => 'InckUserBundle:Badge',
                    'view'      => 'badges',
                    'orderBy'   => 'id',
                    'limit'     => 5,
                ];

            default:
                return null;
        }
    }
}
