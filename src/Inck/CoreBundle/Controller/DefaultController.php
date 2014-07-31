<?php

namespace Inck\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home")
     * @Template()
     */
    public function indexAction()
    {
//        $em = $this->getDoctrine()->getManager();
//        $articles = $em->getRepository('InckArticleBundle:Article')->findBy(array('published' => true), array('published_at' => 'DESC'));
//
//        return array('articles' => $articles);
        return array();
    }
}
