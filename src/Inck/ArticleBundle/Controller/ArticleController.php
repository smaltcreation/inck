<?php

namespace Inck\ArticleBundle\Controller;

use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use DateTime;
use DateTimeZone;
use Inck\ArticleBundle\Entity\Article;
use Inck\ArticleBundle\Entity\Category;
use Inck\ArticleBundle\Entity\ReportRepository;
use Inck\ArticleBundle\Entity\Tag;
use Inck\ArticleBundle\Form\Type\ArticleFilterType;
use Inck\ArticleBundle\Form\Type\ArticleType;
use Inck\UserBundle\Entity\User;
use JMS\SecurityExtraBundle\Annotation\Secure;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * @Route("/article")
 */
class ArticleController extends Controller
{
    /**
     * @Route("/new", name="inck_article_article_new", options={"sitemap" = true})
     * @Secure(roles="ROLE_USER")
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request)
    {
        $article = new Article();

        // Traitement
        return $this->forward('InckArticleBundle:Article:form', array(
            'request'   => $request,
            'article'   => $article,
            'action'    => 'add',
        ));
    }

    /**
     * @Route("/{id}/{slug}/edit", name="inck_article_article_edit", requirements={"id" = "\d+"}, options={"expose"=true})
     * @ParamConverter("article", options={"mapping": {"id": "id", "slug": "slug"}})
     * @Secure(roles="ROLE_USER")
     * @param Request $request
     * @param Article $article
     * @return Response
     */
    public function editAction(Request $request, Article $article)
    {
        // Tentative d'édition d'un article dont on est pas l'auteur,
        // ou tentative d'édition d'un article qui n'est pas un brouillon
        if($this->getUser() !== $article->getAuthor() || $article->getAsDraft() === false)
        {
            throw $this->createNotFoundException("Article inexistant");
        }

        // Traitement
        return $this->forward('InckArticleBundle:Article:form', array(
            'request'   => $request,
            'article'   => $article,
            'action'    => 'edit',
        ));
    }

    /**
     * @Route("/{id}/{slug}", name="inck_article_article_show", requirements={"id" = "\d+"})
     * @ParamConverter("article", options={"mapping": {"id": "id", "slug": "slug"}})
     * @Template()
     * @param Article $article
     * @return array
     */
    public function showAction(Article $article)
    {
        $user = $this->getUser();

        if($article->getAsDraft() && $user !== $article->getAuthor() || !$user && !$article->getPublished())
        {
            throw $this->createNotFoundException("Article inexistant");
        }

        return array(
            'article' => $article,
        );
    }

    /**
     * @Template()
     * @param Request $request
     * @param Article $article
     * @param $action
     * @return array|RedirectResponse
     */
    public function formAction(Request $request, Article $article, $action)
    {
        if(!$article->getImageName())
        {
            $article->setImageFile(null);
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
            $article->setAuthor($this->getUser());
            $article->setUpdatedAt(new \DateTime());

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
            $em = $this->getDoctrine()->getManager();
            $em->persist($article);
            $em->flush();

            $this->get('session')->getFlashBag()->add('success', 'Article enregistré !');

            return $this->redirect($this->generateUrl('inck_article_article_show', array(
                'id'        => $article->getId(),
                'slug'      => $article->getSlug(),
            )));
        }

        // On retourne le formulaire pour la vue
        return array(
            'form'          => $form->createView(),
            'action'        => $action,
            'articleId'     => $article->getId(),
            'articleSlug'   => $article->getSlug(),
        );
    }

    /**
     * @Route("/{id}/{slug}/modal", name="inck_article_article_show_modal", requirements={"id" = "\d+"})
     * @ParamConverter("article", options={"mapping": {"id": "id", "slug": "slug"}})
     * @Template("InckArticleBundle:Article:show_modal.html.twig")
     * @param Article $article
     * @return array
     */
    public function showModalAction(Article $article)
    {
        $user = $this->getUser();

        if(!$user && ($article->getAsDraft() && $user !== $article->getAuthor() || !$user && !$article->getPublished()))
        {
            throw $this->createNotFoundException("Article inexistant");
        }

        return array(
            'article' => $article,
        );
    }

    /**
     * @Template()
     * @param Article $article
     * @return array
     */
    public function scoreAction(Article $article)
    {
        $em = $this->getDoctrine()->getManager();

        $score = array(
            'up'    => 0,
            'down'  => 0,
            'total' => 0,
        );

        $vote = ($this->get('security.context')->isGranted('ROLE_USER'))
            ? $em->getRepository('InckArticleBundle:Vote')->getByArticleAndUser($article, $this->getUser())
            : null;

        foreach($article->getVotes() as $v)
        {
            $score[($v->getUp()) ? 'up' : 'down']++;
            $score['total']++;
        }

        // Signalements
        /** @var ReportRepository $reportRepository */
        $reportRepository = $em->getRepository('InckArticleBundle:Report');
        $reports = $reportRepository->countByArticle($article);

        $reported = ($user = $this->getUser())
            ? ($reportRepository->getByArticleAndUser($article, $user) !== null)
            : false;

        return array(
            'article'   => $article,
            'vote'      => $vote,
            'score'     => $score,
            'reports'   => $reports,
            'reported'  => $reported,
        );
    }

    /**
     * @Template()
     * @param $filters
     * @throws Exception
     * @return array
     */
    public function timelineAction($filters)
    {
        $form = $this->createForm(new ArticleFilterType(), array(
            'search'    => isset($filters['search'])
                ? $filters['search']
                : null,
            'type'      => isset($filters['type'])
                ? $filters['type']
                : null,
        ));

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
     * @param Request $request
     * @param $page
     * @throws Exception
     * @return array
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
            if(!in_array($key, array('categories', 'tags', 'authors', 'order', 'type', 'search')))
            {
                unset($filters[$key]);
            }
        }

        // Gestion du type
        if(!isset($filters['type'])) {
            $filters['type'] = 'published';
        }

        else if($this->getUser())
        {
            if(!in_array($filters['type'], array('published', 'in_moderation'))) {
                $filters['type'] = 'published';
            }
        }

        else if($filters['type'] !== 'published')
        {
            $filters['type'] = 'published';
        }

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
     * @Route("/moderate", name="inck_article_article_moderate", options={"sitemap" = true})
     * @Secure(roles="ROLE_USER")
     * @Template()
     */
    public function moderateAction()
    {
        return array();
    }

    /**
     * @Route("/search", name="inck_article_article_search", options={"sitemap" = true})
     * @Method("get")
     * @Template()
     * @param Request $request
     * @return array
     */
    public function searchAction(Request $request)
    {
        /** @var $em ObjectManager */
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
     * @Route("/{id}/{slug}/delete", name="inck_article_article_delete", requirements={"id" = "\d+"}, options={"expose"=true})
     * @ParamConverter("article", options={"mapping": {"id": "id", "slug": "slug"}})
     * @Secure(roles="ROLE_USER")
     * @param Article $article
     * @return JSONResponse
     */
    public function deleteAction(Article $article)
    {
        try {
            if (!$this->get('security.context')->isGranted('ROLE_ADMIN') && ($this->getUser() !== $article->getAuthor() || !$article->getAsDraft())) {
                throw $this->createNotFoundException("Article inexistant");
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($article);
            $em->flush();

            return new JsonResponse(array('message' => 'Votre article a été supprimé avec succès !'));
        }
        catch(\Exception $e) {
            return new JsonResponse(array('message' => $e->getMessage()), 400);
        }
    }

    /**
     * @Template()
     * @param Article $article
     * @return array
     */
    public function buttonWatchLaterAction(Article $article)
    {
        $watchLater = ($this->get('security.context')->isGranted('ROLE_USER'))
            ? $this->getUser()->getArticlesWatchLater()->contains($article)
            : false;

        return array(
            'watchLater'    => $watchLater,
            'id'            => $article->getId(),
            'slug'          => $article->getSlug(),
        );
    }

    /**
     * @Route("/{id}/{slug}/watch-later", name="inck_article_article_watchLater", requirements={"id" = "\d+"}, options={"expose"=true})
     * @ParamConverter("article", options={"mapping": {"id": "id", "slug": "slug"}})
     * @param Article $article
     * @return JsonResponse
     */
    public function watchLater(Article $article)
    {
        try
        {
            $user = $this->getUser();

            if(!$user)
            {
                throw new \Exception('Vous devez être connecté pour ajouter cet article dans votre liste d\'articles "à regarder plus tard".');
            }

            if($user->getArticlesWatchLater()->contains($article))
            {
                $user->removeArticlesWatchLater($article);
            }

            else
            {
                $user->addArticlesWatchLater($article);
            }

            $em = $this->getDoctrine()->getManager();
            $em->persist($user);
            $em->flush();

            return new JsonResponse(null, 204);
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

    /**
     * @Route("/{id}/{slug}/pdf", name="inck_article_article_pdf")
     * @ParamConverter("article", options={"mapping": {"id": "id", "slug": "slug"}})
     * @Template()
     * @param Article $article
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function pdfAction(Article $article)
    {
        $article->setContent(preg_replace('#<iframe.*?src="(.*?)".*?><\/iframe>#i', '<p><a href"$1">$1</a></p>', $article->getContent()));
        $html = $this->renderView('InckArticleBundle:Article:pdf.html.twig', [
            'article'   => $article,
            'user'      => $this->getUser()
        ]);

        return new Response(
            $this->get('knp_snappy.pdf')->getOutputFromHtml($html),
            Response::HTTP_OK,
            [
                'Content-Type'          => 'application/pdf',
                'Content-Disposition'   => sprintf(
                    'attachment; filename="%s.pdf"',
                    $article->getSlug()
                ),
            ]
        );
    }

    /**
     * @Template("InckArticleBundle:Article:filter.html.twig")
     */
    public function lastAction()
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em
            ->getRepository('InckArticleBundle:Article')
            ->findByFilters(array(
                'type' => 'published',
            ), false, 1);

        return array(
            'articles'      => $articles,
            'totalArticles' => count($articles),
            'totalPages'    => count($articles),
        );
    }

    /**
     * @Template()
     * @param Article $article
     * @throws Exception
     * @return array
     */
    public function featuredAction(Article $article)
    {
        $em = $this->getDoctrine()->getManager();

        $articles = $em
            ->getRepository('InckArticleBundle:Article')
            ->findByFilters(array(
                'type'  => 'published',
                'not'   => $article->getId(),
            ), false, 25);

        return array(
            'articles' => $articles,
        );
    }

    /**
     * @Route("/{id}/{slug}/publish", name="inck_article_article_publish", requirements={"id" = "\d+"})
     * @ParamConverter("article", options={"mapping": {"id": "id", "slug": "slug"}})
     * @Secure(roles="ROLE_ADMIN")
     * @param Article $article
     * @return RedirectResponse
     */
    public function publish(Article $article)
    {
        try {
            if (!$this->get('security.context')->isGranted('ROLE_ADMIN') && !$article->getAsDraft())
            {
                throw $this->Exception("Cet article ne peut pas être publié.");
            }

            $em = $this->getDoctrine()->getManager();
            $article->setApproved(true);
            $article->setPublished(true);
            $article->setAsDraft(false);
            $article->setPublishedAt(new \DateTime('now'));
            $em->persist($article);
            $em->flush();

            $this->get('session')->getFlashBag()->add(
                'success',
                'Article publié avec succès !'
            );
        }
        catch(\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'error',
                $e
            );
        }

        return $this->redirect($this->generateUrl('inck_core_admin_index'));
    }

    /**
     * @Route("/{id}/{slug}/send", name="inck_article_article_send", requirements={"id" = "\d+"}, options={"expose"=true}))
     * @ParamConverter("article", options={"mapping": {"id": "id", "slug": "slug"}})
     * @Secure(roles="ROLE_USER")
     * @param Article $article
     * @return JSONResponse
     */
    public function send(Article $article)
    {
        try {
            if(($this->getUser() !== $article->getAuthor() && !$this->get('security.context')->isGranted('ROLE_ADMIN')) || $article->getAsDraft() === false)
            {
                throw $this->createNotFoundException("Cet article ne peut pas être envoyé à la modération !");
            }

            $em = $this->getDoctrine()->getManager();
            $article->setApproved(true);
            $article->setAsDraft(false);
            $article->setPostedAt(new \DateTime('now'));
            $em->persist($article);
            $em->flush();

            return new JsonResponse(array('message' => 'Votre article a été envoyé à la modération avec succès !'));
        }
        catch(\Exception $e) {
            return new JsonResponse(array('message' => $e->getMessage()), 400);
        }
    }
}
