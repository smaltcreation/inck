<?php

namespace Inck\ArticleBundle\Controller;

use Doctrine\Common\Collections\ArrayCollection;
use Inck\ArticleBundle\Entity\Article;
use Inck\ArticleBundle\Entity\Category;
use Inck\ArticleBundle\Entity\Tag;
use Inck\ArticleBundle\Entity\Vote;
use Inck\ArticleBundle\Form\Type\ArticleFilterType;
use Inck\ArticleBundle\Form\Type\ArticleType;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
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
//        $values = $request->request->get('inck_articlebundle_article');
//        $deleteImage = ($values['image']['delete'] && $article->getImageName());
//
//        if($deleteImage)
//        {
//            $article->savePreviousImageName();
//        }

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
//            if($deleteImage)
//            {
//                unlink(sprintf(
//                    "%s/%s",
//                    $this->container->getParameter('upload.article_image.upload_destination'),
//                    $article->getPreviousImageName()
//                ));
//
//                $article->setImageName(null);
//            }

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
    public function showAction($id)
    {
        try
        {
            $em = $this->getDoctrine()->getManager();
            $user = $this->getUser();

            // Récupération de l'article
            /** @var $article Article */
            $article = $em->getRepository('InckArticleBundle:Article')->find($id);

            // Article inexistant
            if(!$article)
            {
                throw new \Exception("Article inexistant.");
            }

            // Article "brouillon" ou en modération
            else if($article->getAsDraft() && $user !== $article->getAuthor()
                || !$user && !$article->getPublished())
            {
                throw new \Exception("Article indisponible.");
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
     * @Route("/{id}/modal", name="inck_article_article_show_modal", requirements={"id" = "\d+"})
     * @Template("InckArticleBundle:Article:show_modal.html.twig")
     */
    public function showModalAction($id)
    {
        try
        {
            // Récupération de l'article
            $em = $this->getDoctrine()->getManager();
            /** @var $article Article */
            $article = $em->getRepository('InckArticleBundle:Article')->find($id);

            // Article inexistant
            if(!$article)
            {
                throw new \Exception("Article inexistant.");
            }

            // Article "brouillon"
            else if($article->getAsDraft())
            {
                if($this->getUser() !== $article->getAuthor())
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
     * @var $article Article
     * @Template()
     * @return array
     */
    public function socialAction($article)
    {
        $em = $this->getDoctrine()->getManager();

        $score = array(
            'up'    => 0,
            'down'  => 0,
            'total' => 0,
        );

        /** @var $vote Vote|null */
        $vote = ($this->get('security.context')->isGranted('ROLE_USER'))
            ? $em->getRepository('InckArticleBundle:Vote')->getByArticleAndUser($article, $this->getUser())
            : null;

        /** @var $v Vote */
        foreach($article->getVotes() as $v)
        {
            $score[($v->getUp()) ? 'up' : 'down']++;
            $score['total']++;
        }

        return array(
            'article'   => $article,
            'vote'      => $vote,
            'score'     => $score,
        );
    }

    /**
     * @Template()
     */
    public function timelineAction(Request $request, $type = 'published', $author = null, $category = null, $tag = null, $offset = null, $limit = 10)
    {
        try
        {
            $em = $this->getDoctrine()->getManager();
            $articles = $em->getRepository('InckArticleBundle:Article')->superQuery($type, $author, $category, $tag, $offset, $limit);
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
     * @Route("/moderate", name="inck_article_article_moderate")
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function moderateAction(Request $request)
    {
        $form = $this->createForm(new ArticleFilterType());
        $form->handleRequest($request);

        $categories = null;
        $tags       = null;

        // Si on a sélectionné des filtres
        if($form->isValid())
        {
            $data = $form->getData();

            if(count($data['categories']) !== 0)
            {
                $categories = array();

                /** @var $category Category */
                foreach($data['categories'] as $category)
                {
                    $categories[] = $category->getId();
                }
            }

            if(count($data['tags']) !== 0)
            {
                $tags = array();

                /** @var $tag Tag */
                foreach($data['tags'] as $tag)
                {
                    $tags[] = $tag->getId();
                }
            }
        }

        return array(
            'form'          => $form->createView(),
            'categories'    => $categories,
            'tags'          => $tags,
        );
    }
}
