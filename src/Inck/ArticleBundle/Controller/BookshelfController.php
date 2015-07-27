<?php

namespace Inck\ArticleBundle\Controller;

use DateTime;
use Doctrine\Common\Persistence\ObjectManager;
use Exception;
use Inck\ArticleBundle\Entity\Article;
use Inck\ArticleBundle\Entity\Bookshelf;
use Inck\ArticleBundle\Entity\Category;
use Inck\ArticleBundle\Entity\ReportRepository;
use Inck\ArticleBundle\Entity\Tag;
use Inck\ArticleBundle\Event\ArticleEvent;
use Inck\ArticleBundle\Form\Type\ArticleFilterType;
use Inck\ArticleBundle\Form\Type\ArticleType;
use Inck\ArticleBundle\Form\Type\BookshelfType;
use Inck\UserBundle\Entity\Activity\Article\DeleteArticleActivity;
use Inck\UserBundle\Entity\Activity\Article\PublishArticleActivity;
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
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;

/**
 * @Route("/bookshelf")
 */
class BookshelfController extends Controller
{
    /**
     * @Route("/new", name="article_bookshelf_bookshelfs_new", options={"sitemap" = true})
     * @Secure(roles="ROLE_USER")
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request)
    {
        $bookshelf = new Bookshelf();

        // Traitement
        return $this->forward('InckArticleBundle:Bookshelf:form', array(
            'request'   => $request,
            'bookshelf'   => $bookshelf,
            'action'    => 'add',
        ));
    }


    /**
     * @Route(name="inck_bookshelf_add_to_bookshelf_and_show", requirements={"id" = "\d+"})
     * @ParamConverter("article", options={"mapping": {"id": "id", "bookshelf": "bookshelf"}})
     * @param $id
     * @param Bookshelf $bookshelf
     * @return RedirectResponse
     */
    public function addToBookshelfAndShowAction($id, Bookshelf $bookshelf)
    {
        $article = this()->getArticle($id);

        if(!$bookshelf->getArticles()->contains($article))
        {
            $bookshelf->addArticle($article);
            $em = $this->getDoctrine()->getManager();
            $em->flush();
            dump($bookshelf);
        }
    }

    /**
     * @Route("/{id}", name="inck_article_bookshelf_show", requirements={"id"})
     * @ParamConverter("article", options={"mapping": {"id": "id", "slug": "slug"}})
     * @Template()
     * @param Bookshelf $bookshelf
     * @return array
     */
    public function showAction(Bookshelf $bookshelf)
    {
        $user = $this->getUser();

        return array(
            'bookshelf' => $bookshelf,
            'user' => $user,
        );
    }

    /**
     * @Template()
     * @param Request $request
     * @param Bookshelf $bookshelf
     * @param $action
     * @return array|RedirectResponse
     */
    public function formAction(Request $request, Bookshelf $bookshelf, $action)
    {
        // Création du formulaire
        $form = $this->createForm(new BookshelfType(), $bookshelf);
        // Formulaire envoyé et valide
        $form->handleRequest($request);
        if($form->isValid())
        {
            $bookshelf->setUser($this->getUser());
            $em = $this->getDoctrine()->getManager();
            $em->persist($bookshelf);
            $em->flush();
            $this->get('session')->getFlashBag()->add('success', 'l\'étagère a bien été enregistrée !');
            return $this->redirect($this->generateUrl('inck_profile_profile_show'));
        }
        // On retourne le formulaire pour la vue
        return array(
            'form'          => $form->createView(),
            'action'        => $action,
            'bookshelfId'     => $bookshelf->getId(),
        );
    }

    public function getArticle($id)
    {
        $em = $this->getDoctrine()->getManager();
        $articles = $em->getRepository('InckArticleBundle:Article')->findOneBy(array('id' => $id));
        return array(
            'article' => $articles,
        );
    }
}