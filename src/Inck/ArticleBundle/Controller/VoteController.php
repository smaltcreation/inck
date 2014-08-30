<?php

namespace Inck\ArticleBundle\Controller;

use Exception;
use Inck\ArticleBundle\Entity\Vote;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

class VoteController extends Controller
{
    /**
     * @Route("/vote/{up}/article/{id}", name="inck_article_vote_new", requirements={"id" = "\d+", "up" = "0|1"}, options={"expose"=true})
     * @Secure(roles="ROLE_USER")
     */
    public function save($id, $up)
    {
        try
        {
            $em = $this->getDoctrine()->getManager();

            // Récupération de l'article
            $article = $em->getRepository('InckArticleBundle:Article')->find($id);

            if(!$article)
            {
                throw new Exception("Article inexistant.");
            }

            // Récupération d'un vote existant
            $user = $this->get('security.context')->getToken()->getUser();

            /** @var $vote Vote|null */
            $vote = $em->getRepository('InckArticleBundle:Vote')->getByArticleAndUser($article, $user);

            // Il s'agit d'un nouveau vote
            if(!$vote)
            {
                $vote = new Vote();
                $vote->setArticle($article);
                $vote->setUser($user);
                $vote->setSubmittedOn(new \DateTime());
                $vote->setUp($up);
                $em->persist($vote);
            }

            // L'utilisateur change d'avis
            else if($vote->getUp() != $up)
            {
                $vote->setSubmittedOn(new \DateTime());
                $vote->setUp($up);
                $em->persist($vote);
            }

            // L'utilisateur annule son vote
            else
            {
                $em->remove($vote);
            }

            // Sauvegarde
            $em->flush();

            return new JsonResponse(null, 204);
        }

        catch(Exception $e)
        {
            return new JsonResponse(array(
                'modal'   => $this->renderView('InckArticleBundle:Vote:error.html.twig', array(
                    'message'   => $e->getMessage(),
                )),
            ), 400);
        }
    }
}
