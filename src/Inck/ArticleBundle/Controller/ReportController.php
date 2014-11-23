<?php

namespace Inck\ArticleBundle\Controller;

use Inck\ArticleBundle\Entity\Article;
use Inck\ArticleBundle\Entity\Report;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class ReportController extends Controller
{
    /**
     * @Route("/report/article/{id}", name="inck_article_report_new", requirements={"id" = "\d+"}, options={"expose"=true})
     * @param Article $article
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function save(Article $article)
{
        // Utilisateur
        $user = $this->getUser();

        if(!$user) {
            $this->createAccessDeniedException('Vous devez être connecté pour voter.');
        }

        $em = $this->getDoctrine()->getManager();

        // Récupération d'un signalement existant
        $report = $em->getRepository('InckArticleBundle:Report')->getByArticleAndUser($article, $user);

        // Il s'agit d'un nouveau signalement
        if(!$report) {
            $report = new Report();

            $report
                ->setArticle($article)
                ->setUser($user);

            $em->persist($report);
        }


        // L'utilisateur annule son signalement
        else {
            $em->remove($report);
        }

        // Sauvegarde
        $em->flush();

        return new JsonResponse(null, 204);
    }
}
