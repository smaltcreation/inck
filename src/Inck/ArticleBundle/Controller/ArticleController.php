<?php

namespace Inck\ArticleBundle\Controller;

use Inck\ArticleBundle\Entity\Article;
use Inck\ArticleBundle\Form\Type\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

/**
 * @Route("/article")
 */
class ArticleController extends Controller
{
    /**
     * @Route("/new", name="inck_article_article_new", defaults={"id" = 0})
     * @Route("/{id}/edit", name="inck_article_article_edit", requirements={"id" = "\d+"})
     * @Template()
     * @Secure(roles="ROLE_USER")
     */
    public function formAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        $user = $this->get('security.context')->getToken()->getUser();

        // Récupération de l'article en cas d'édition
        $article = ($id === 0)
            ? new Article()
            : $em->getRepository('InckArticleBundle:Article')->find($id)
        ;

        // Article inexistant
        if(!$article)
        {
            $this->get('session')->getFlashBag()->add(
                'danger',
                "Article inexistant."
            );

            return $this->redirect($this->generateUrl('home'));
        }

        // Tentative d'édition d'un article dont on est pas l'auteur
        else if($id !== 0 && $user !== $article->getAuthor())
        {
            $this->get('session')->getFlashBag()->add(
                'danger',
                "Cet article ne vous appartient pas."
            );

            return $this->redirect($this->generateUrl('home'));
        }

        // Tentative d'édition d'un article qui n'est pas un brouillon
        else if($article->getAsDraft() === false)
        {
            $this->get('session')->getFlashBag()->add(
                'danger',
                "Cet article ne peut pas être édité."
            );

            return $this->redirect($this->generateUrl('home'));
        }

        // Création du formulaire
        $form = $this->createForm(new ArticleType(), $article);

        // Suppression de l'image
        $values = $request->request->get('inck_articlebundle_article');
        $deleteImage = ($values['image']['delete'] && $article->getImageName());

        if($deleteImage)
        {
            $article->savePreviousImageName();
        }

        // Formulaire envoyé et valide
        $form->handleRequest($request);
        if($form->isValid())
        {
            $article->setPublished(false);
            $article->setAuthor($user);

            // On a cliqué sur le bouton "publier"
            if($form->get('actions')->get('publish')->isClicked())
            {
                $article->setPostedAt(new \DateTime());
                $article->setAsDraft(false);
            }

            // On a cliqué sur le bouton "brouillon"
            else
            {
                $article->setAsDraft(true);
            }

            // Suppression de l'image
            if($deleteImage)
            {
                unlink(sprintf(
                    "%s/%s",
                    $this->container->getParameter('upload.article_image.upload_destination'),
                    $article->getPreviousImageName()
                ));

                $article->setImageName(null);
            }

            // Enregistrement et redirection
            $em->persist($article);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'Article enregistré !'
            );

            return $this->redirect($this->generateUrl('inck_article_article_show', array(
                'id' => $article->getId(),
            )));
        }

        // On retourne le formulaire pour la vue
        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/{id}", name="inck_article_article_show", requirements={"id" = "\d+"})
     * @Template()
     */
    public function showAction(Request $request, $id)
    {
        try
        {
            // Récupération de l'article
            $em = $this->getDoctrine()->getManager();
            /** @var Article $article */
            $article = $em->getRepository('InckArticleBundle:Article')->find($id);

            // Article inexistant
            if(!$article)
            {
                throw new \Exception("Article inexistant.");
            }

            // Article "brouillon"
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

    /**
     * @Template()
     */
    public function timelineAction(Request $request, $author = null, $category = null, $tag =null, $offset = null, $limit =null)
    {
        try
        {
            $em = $this->getDoctrine()->getManager();
            $articles = $em->getRepository('InckArticleBundle:Article')->superQuery('published', $author, $category, $tag, $offset, $limit);
            return array(
                'articles' => $articles,
            );
        }
        catch(\Exception $e)
        {
            $this->get('session')->getFlashBag()->add(
                'danger',
                $e->getMessage()
            );
            $referer = $request->headers->get('referer');
            return new RedirectResponse($referer);
        }
    }

    /**
     * @Route("/article/{id}/vote/{up}", name="inck_article_article_vote", requirements={"id" = "\d+", "up" = "0|1"}, options={"expose"=true})
     */
    public function save($id, $up)
    {
        // Renvoie des données
        return new JsonResponse(array(
            'test'   => true,
        ));
    }
}
