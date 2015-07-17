<?php

namespace Inck\ArticleBundle\Controller;

use Exception;
use Inck\ArticleBundle\Entity\Vote;
use Inck\UserBundle\Entity\Activity;
use Inck\UserBundle\Entity\Activity\Vote\VoteActivity;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class VoteController extends Controller
{
    /**
     * @Route("/vote/{up}/article/{id}", name="inck_article_vote_new", requirements={"id" = "\d+", "up" = "0|1"}, options={"expose"=true})
     */
    public function save($id, $up)
    {
        try {
            // Utilisateur
            $user = $this->getUser();

            if(!$user) {
                throw new Exception("Vous devez être connecté pour voter.");
            }

            $em = $this->getDoctrine()->getManager();

            // Récupération de l'article
            $article = $em->getRepository('InckArticleBundle:Article')->find($id);

            if(!$article) {
                throw new Exception("Article inexistant.");
            }

            // Récupération d'un vote existant
            /** @var $vote Vote|null */
            $vote = $em->getRepository('InckArticleBundle:Vote')->getByArticleAndUser($article, $user);


            if(!$vote) {
                // Il s'agit d'un nouveau vote
                $vote = new Vote();
                $vote->setArticle($article);
                $vote->setUser($user);
                $vote->setSubmittedOn(new \DateTime());
                $vote->setUp($up);
                $em->persist($vote);

                // On enregistre une nouvelle activité de l'utilisateur
                $activity = new VoteActivity($user, $vote);
                $em->persist($activity);
            } else if($vote->getUp() != $up) {
                // L'utilisateur change d'avis
                $vote->setSubmittedOn(new \DateTime());
                $vote->setUp($up);
                $em->persist($vote);
            }
            else {
                // L'utilisateur annule son vote
                $em->remove($vote);
            }

            // Sauvegarde
            $em->flush();

            return new JsonResponse(null, 204);
        } catch(Exception $e) {
            return new JsonResponse(array(
                'modal'   => $this->renderView('InckArticleBundle:Vote:error.html.twig', array(
                    'message'   => $e->getMessage(),
                )),
            ), 400);
        }
    }
}
