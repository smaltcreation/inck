<?php

namespace Inck\ArticleBundle\Controller;

use Inck\ArticleBundle\Entity\Article;
use Inck\ArticleBundle\Form\Type\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/article")
 */
class ArticleController extends Controller
{
    /**
     * @Route("/new", name="article_new", defaults={"id" = 0})
     * @Route("/{id}/edit", name="article_edit", requirements={"id" = "\d+"})
     * @Template()
     * @Secure(roles="ROLE_USER")
     */
    public function formAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        $article = ($id === 0)
            ? new Article()
            : $em->getRepository('InckArticleBundle:Article')->find($id)
        ;

        if($id !== 0 && $user !== $article->getAuthor())
        {
            $this->get('session')->getFlashBag()->add(
                'danger',
                "Cet article ne vous appartient pas."
            );

            return $this->redirect($this->generateUrl('home'));
        }

        $form = $this->createForm(new ArticleType(), $article);
        $form->handleRequest($request);

        if($form->isValid())
        {
            $article->setApproved(false);
            $article->setAuthor($user);

            if($form->get('actions')->get('publish')->isClicked())
            {
                $article->setPublished(true);
                $article->setPublishedAt(new \DateTime());
                $article->setAsDraft(false);
            }

            else
            {
                $article->setPublished(false);
                $article->setPublishedAt(null);
                $article->setAsDraft(true);
            }

            $em->persist($article);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'Article enregistré !'
            );

            return $this->redirect($this->generateUrl('article_show', array(
                'id' => $article->getId(),
            )));
        }

        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/{id}", name="article_show", requirements={"id" = "\d+"})
     * @Template()
     */
    public function showAction(Request $request, $id)
    {
        try
        {
            $em = $this->getDoctrine()->getManager();
            $article = $em->getRepository('InckArticleBundle:Article')->find($id);

            if(!$article)
            {
                throw new \Exception("Article inexistant.");
            }

            else if($article->getAsDraft())
            {
                $user = $this->get('security.context')->getToken()->getUser();

                if($user !== $article->getAuthor())
                {
                    throw new \Exception("Article indisponible.");
                }
            }

            return array(
                'article' => $article,
            );
        }

        catch(\Exception $e)
        {
            $this->get('session')->getFlashBag()->add(
                'danger',
                $e->getMessage()
            );

            return $this->redirect($this->generateUrl('home'));
        }
    }
}
