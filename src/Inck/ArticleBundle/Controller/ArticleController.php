<?php

namespace Inck\ArticleBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Inck\ArticleBundle\Entity\Article;
use Inck\ArticleBundle\Entity\Category;
use Inck\ArticleBundle\Entity\Tag;
use Inck\ArticleBundle\Entity\Vote;
use Inck\ArticleBundle\Form\Type\ArticleFilterType;
use Inck\ArticleBundle\Form\Type\ArticleType;
use Inck\UserBundle\Entity\User;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;

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
        $user = $this->getUser();

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
        $deleteImage = ($values['imageFile']['delete'] && $article->getImageName());

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
            /** @var SubmitButton $publish */
            $publish = $form->get('actions')->get('publish');

            if($publish->isClicked())
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
                'slug' => $article->getSlug(),
                'date' => ($article->getPublishedAt())
                        ? $article->getPublishedAt()->format('Y-m-d')
                        : $article->getPostedAt()->format('Y-m-d')
            )));
        }

        // On retourne le formulaire pour la vue
        return array(
            'form' => $form->createView(),
        );
    }

    /**
     * @Route("/{id}/{slug}/{date}", name="inck_article_article_show", requirements={"id" = "\d+"})
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
    public function timelineAction($filters)
    {
        $form = $this->createForm(new ArticleFilterType());
        $em = $this->getDoctrine()->getManager();

        list($articles, $totalArticles, $totalPages) = $em
            ->getRepository('InckArticleBundle:Article')
            ->findByFilters($filters);

        // Filtres sélectionnés par défaut
        $selectedFilters = array('category', 'tag', 'author');
        $selected = array();

        foreach($filters as $filter => $entity)
        {
            if(in_array($filter, $selectedFilters))
            {
                if($filter === 'author')
                {
                    /** @var User $entity */
                    $name = $entity->getUsername();
                }

                else
                {
                    /** @var Category|Tag $entity */
                    $name = $entity->getName();
                }

                $selected[$filter] = array(
                    'id'        => $entity->getId(),
                    'text'      => $name,
                    'locked'    => true,
                );
            }
        }

        return array(
            'form'          => $form->createView(),
            'selected'      => $selected,
            'articles'      => $articles,
            'totalArticles' => $totalArticles,
            'totalPages'    => $totalPages,
        );
    }

    /**
     * @Route("/filter/{page}", name="inck_article_article_filter", defaults={"page" = 1}, options={"expose"=true})
     * @Method("POST")
     * @Template()
     */
    public function filterAction(Request $request, $page)
    {
        /** @var $em ObjectManager */
        $em = $this->getDoctrine()->getManager();

        // Filtres reçus
        $filters = $request->request->get('filters');

        if(!is_array($filters))
        {
            $filters = array();
        }

        // Suppression des filtres vides
        foreach($filters as $key => $data)
        {
            if(!$data)
            {
                unset($filters[$key]);
            }
        }

        // Suppression des filtres qui auraient pu être ajoutés
        $keys = array_keys($filters);
        foreach($keys as $key)
        {
            if(!in_array($key, array('categories', 'tags', 'authors')))
            {
                unset($filters[$key]);
            }
        }

        // Ajout du type
        $filters['type'] = 'published';

        // Récupération des articles
        list($articles, $totalArticles, $totalPages) = $em
            ->getRepository('InckArticleBundle:Article')
            ->findByFilters($filters, $page);

        return array(
            'articles'      => $articles,
            'totalArticles' => $totalArticles,
            'totalPages'    => $totalPages,
        );
    }

    /**
     * @Route("/moderate", name="inck_article_article_moderate")
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function moderateAction()
    {
        return array();
    }

    /**
     * @Route("/search", name="inck_article_article_search")
     * @Method("get")
     * @Template()
     */
    public function searchAction(Request $request)
    {
        /** @var $em ObjectManager */
        $em = $this->getDoctrine()->getManager();
        $search = $request->query->get('q');

        $filters = array(
            'type'      => 'published',
            'search'    => $search,
        );

        return array(
            'filters'   => $filters,
            'search'    => $search,
        );
    }

    /**
     * @Route("/{id}/delete", name="inck_article_article_delete", requirements={"id" = "\d+"})
     */
    public function deleteAction($id)
    {
        try
        {
            $em = $this->getDoctrine()->getManager();
            $article = $em->getRepository("InckArticleBundle:Article")->find($id);
            $user = $this->getUser();

            if(!$article)
            {
                throw new \Exception("Article inexistant.");
            }

            if(!$this->get('security.context')->isGranted('ROLE_USER'))
            {
                $this->get('session')->getFlashBag()->add(
                    'warning',
                    'Vous devez vous identifier et avoir les droits nécessaires pour supprimer cet article.'
                );
                return $this->redirect($this->generateUrl('fos_user_security_login'));
            }

            if (!$this->get('security.context')->isGranted('ROLE_ADMIN'))
            {
                if ($user != $article->getAuthor())
                {
                    throw new \Exception("Cet article ne vous appartient pas.");
                }
                else if ($article->getAsDraft() == 0)
                {
                    throw new \Exception("Vous ne pouvez pas supprimer un article après sa publication.");
                }
            }

            $em->remove($article);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'Article supprimé avec succès !'
            );

            return $this->redirect($this->generateUrl('fos_user_profile_show'));
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
    public function buttonToBeSeenAction($article)
    {
        $toBeSeen = false;
        if($this->get('security.context')->isGranted('ROLE_USER'))
        {
            $user = $this->get('security.context')->getToken()->getUser();
            $toBeSeen = $user->getArticlesToBeSeen()->contains($article);
        }

        return array(
            'toBeSeen' => $toBeSeen,
            'id' => $article->getId()
        );
    }

    /**
     * @Route("/article/{id}/to-be-seen", name="inck_article_article_toBeSeen", requirements={"id" = "\d+"}, options={"expose"=true})
     */
    public function toBeSeen($id)
    {
        try
        {
            $user = $this->getUser();
            if(!$user)
            {
                throw new \Exception("Vous devez être connecté pour ajouter cet article dans votre liste \"à regarder plus tard\".");
            }

            $em = $this->getDoctrine()->getManager();
            $article = $em->getRepository('InckArticleBundle:Article')->find($id);
            if(!$article)
            {
                throw new \Exception("Article inexistant.");
            }

            $toBeSeen = $user->getArticlesToBeSeen()->contains($article);
            if(!$toBeSeen)
            {
                $user->addArticlesToBeSeen($article);
            }
            else
            {
                $user->removeArticlesToBeSeen($article);
            }

            $em->persist($user);
            $em->flush();

            return new JsonResponse('Article ajouté avec succès', 204);
        }

        catch(\Exception $e)
        {
            return new JsonResponse(array(
                'modal'   => $this->renderView('InckArticleBundle:Article:error.html.twig', array(
                        'message'   => $e->getMessage(),
                    )),
            ), 400);
        }
    }
}