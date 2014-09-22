<?php

namespace Inck\BidBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class DefaultController extends Controller
{
    public function indexAction($name)
    {
        return $this->render('InckBidBundle:Default:index.html.twig', array('name' => $name));
    }
}
