<?php

namespace Inck\ArticleBundle\Controller;

use Inck\ArticleBundle\Entity\Article;
use Inck\ArticleBundle\Form\Type\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;

class ArticleController extends Controller
{
    /**
     * @Route("/article/new", name="article_new")
     * @Template()
     * @todo vérifier que l'utilisateur soit bien connecté
     */
    public function formAction(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(new ArticleType(), $article);
        $form->handleRequest($request);

        if($form->isValid())
        {
            $article->setApproved(false);

            if($form->get('actions')->get('publish')->isClicked())
            {
                $article->setPublished(true);
                $article->setPublishedAt(new \DateTime());
                $article->setAsDraft(false);
                $route = 'article_show';
            }

            else
            {
                $article->setPublished(false);
                $article->setPublishedAt(null);
                $article->setAsDraft(true);
                $route = 'profile'; // @todo utiliser le nom de la route qui affiche le profil
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            return $this->redirect($this->generateUrl("home", array( // @todo utiliser $route
                'id' => $article->getId(),
            )));
        }

        return array(
            'form' => $form->createView(),
        );
    }
}
