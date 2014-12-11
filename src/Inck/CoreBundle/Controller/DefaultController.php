<?php

namespace Inck\CoreBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class DefaultController extends Controller
{
    /**
     * @Route("/", name="home", options={"sitemap" = true})
     * @Template()
     */
    public function indexAction()
    {
        return array();
    }

    /**
     * @Route("/about", name="inck_core_default_about", options={"sitemap" = true})
     * @Template()
     */
    public function aboutAction()
    {
        return array();
    }
}
