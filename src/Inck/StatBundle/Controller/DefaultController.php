<?php

namespace Inck\StatBundle\Controller;

use FOS\UserBundle\Propel\User;
use Inck\StatBundle\Entity\UserStat;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Inck\StatBundle\Entity\ArticleStat;

class DefaultController extends Controller
{
    public function indexAction($idarticle)
    {
        $em = $this->getDoctrine()->getManager();
        $repository = $em->getRepository('InckArticleBundle:Article');
        $wantedArticle = $repository->find($idarticle);
        $wantedAuthor = $wantedArticle->getAuthor();

        $results = ArticleStat::getStatVotes($wantedArticle);

        return $this->render('InckStatBundle:Default:index.html.twig', array('idarticle' => $idarticle, 'results' => $results, 'author' => $wantedAuthor));
    }
}