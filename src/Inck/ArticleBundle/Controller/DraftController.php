<?php

namespace Inck\ArticleBundle\Controller;

use Inck\ArticleBundle\Entity\Article;
use Inck\ArticleBundle\Form\Type\ArticleType;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/article")
 */
class DraftController extends Controller
{
    /**
     * @Route("/draft/new", name="inck_article_draft_new", options={"expose"=true})
     * @Method("POST")
     * @Secure(roles="ROLE_USER")
     * @param Request $request
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function newDraftAction(Request $request)
    {
        $article = new Article();
        $form = $this->createForm(new ArticleType(), $article);
        $form->handleRequest($request);

        if(!$form->isValid())
        {
            return new JsonResponse(array(
                'valid' => false,
            ));
        }

        $article->setPublished(false);
        $article->setAuthor($this->getUser());
        $article->setUpdatedAt(new \DateTime());
        $article->setAsDraft(true);

        $em = $this->getDoctrine()->getManager();
        $em->persist($article);
        $em->flush();

        return new JsonResponse(array(
            'valid' => true,
            'id'    => $article->getId(),
            'slug'  => $article->getSlug(),
        ));
    }

    /**
     * @Route("/draft/edit/{id}", name="inck_article_draft_edit", options={"expose"=true})
     * @Method("POST")
     * @Secure(roles="ROLE_USER")
     * @param Request $request
     * @param Article $article
     * @return \Symfony\Component\HttpFoundation\JsonResponse
     */
    public function editDraftAction(Request $request, Article $article)
    {
        $form = $this->createForm(new ArticleType(), $article);
        $form->handleRequest($request);

        if($form->isValid())
        {
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();
        }

        return new JsonResponse(null, 204);
    }
}
