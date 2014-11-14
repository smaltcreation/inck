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
        $em = $this->container->get('doctrine')->getManager();
        /** @var ArticleRepository $repository */
        $repository = $em->getRepository('InckArticleBundle:Article');

        $postedArticles = $repository->findByFilters(array(
            'type'      => 'posted',
        ), $page = false, $limit = 3);

        /** @var UserRepository $repository */
        $repository = $em->getRepository('InckUserBundle:User');
        $users = $repository->findBy(array('enabled' => true), array('id' => 'desc'), 3);

        return array(
            'postedArticles' => $postedArticles,
            'users' => $users
        );
    }

    /**
     * @Route("/article", name="inck_core_admin_article")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function articleAction()
    {
        return array();
    }

    /**
     * @Route("/category", name="inck_core_admin_category")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function categoryAction()
    {
        return array();
    }

    /**
     * @Route("/badge", name="inck_core_admin_badge")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function badgeAction()
    {
        return array();
    }

    /**
     * @Route("/user", name="inck_core_admin_user")
     * @Secure(roles="ROLE_ADMIN")
     * @Template()
     */
    public function userAction()
    {
        return array();
    }
}
