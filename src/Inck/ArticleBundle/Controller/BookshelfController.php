<?php

namespace Inck\ArticleBundle\Controller;

use Inck\ArticleBundle\Entity\Article;
use Inck\ArticleBundle\Entity\Bookshelf;
use Inck\ArticleBundle\Form\Type\BookshelfType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\ParamConverter;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use JMS\SecurityExtraBundle\Annotation\Secure;

/**
 * @Route("/bookshelf")
 * @Security("has_role('ROLE_USER')")
 */
class BookshelfController extends Controller
{
    /**
     * @Route("/new", name="inck_article_bookshelf_new")
     * @Secure(roles="ROLE_USER")
     * @param Request $request
     * @return Response
     */
    public function addAction(Request $request)
    {
        return $this->forward('InckArticleBundle:Bookshelf:form', array(
            'request'   => $request,
            'bookshelf' => new Bookshelf(),
            'action'    => 'add',
        ));
    }

    /**
     * @Route("/{id}", name="inck_article_bookshelf_show", requirements={"id"})
     * @ParamConverter("bookshelf", options={"mapping": {"id": "id"}})
     * @Template()
     * @param Bookshelf $bookshelf
     * @return array
     */
    public function showAction(Bookshelf $bookshelf)
    {
        if (!$bookshelf->getShare()) {
            if($this->getUser() !== $bookshelf->getUser()) {
                throw $this->createAccessDeniedException("Vous n'avez pas les droits d'accès cette bibliothèque !");
            }
        }

        return array(
            'bookshelf' => $bookshelf,
        );
    }

    /**
     * @Route("/{id}/edit", name="inck_article_bookshelf_edit")
     * @ParamConverter("bookshelf", options={"mapping": {"id": "id"}})
     * @Secure(roles="ROLE_USER")
     * @param Request $request
     * @return Response
     */
    public function editAction(Request $request, Bookshelf $bookshelf)
    {
        if($bookshelf->getUser() != $this->getUser()){
            throw $this->createAccessDeniedException('Vous n\'êtes pas le propriétaire de cette bibliothèque');
        }

        return $this->forward('InckArticleBundle:Bookshelf:form', array(
            'request'   => $request,
            'bookshelf'   => $bookshelf,
            'action'    => 'edit',
        ));
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

        if($form->isValid()) {
            $em = $this->getDoctrine()->getManager();

            $bookshelf->setUser($this->getUser());
            $em->persist($bookshelf);
            $em->flush();

            if($action == "edit"){
                $this->get('session')->getFlashBag()->add('success', 'La bibliothèque a bien été modifiée !');
            }else{
                $this->get('session')->getFlashBag()->add('success', 'La bibliothèque a bien été ajoutée !');
            }

            return $this->redirect($this->generateUrl('inck_article_bookshelf_show', array('id' => $bookshelf->getId())));
        }
        // On retourne le formulaire pour la vue
        return array(
            'form'          => $form->createView(),
            'action'        => $action,
            'bookshelf'     => $bookshelf,
        );
    }

    /**
     * @Route("/{id}", name="inck_article_bookshelf_delete", requirements={"id" = "\d+"}, options={"expose"=true}, condition="request.isXmlHttpRequest()")
     * @Method("DELETE")
     * @ParamConverter("bookshelf", options={"mapping": {"id": "id"}})
     * @Secure(roles="ROLE_USER")
     * @param Bookshelf $bookshelf
     * @return JsonResponse
     */
    public function deleteAction(Bookshelf $bookshelf)
    {
        try {
            if ($this->getUser() !== $bookshelf->getUser()) {
                throw $this->createAccessDeniedException("Vous n'avez pas le droit de supprimé cette bibliothèque !");
            }

            $em = $this->getDoctrine()->getManager();
            $em->remove($bookshelf);
            $em->flush();

            return new JsonResponse(array('message' => 'Article a été supprimé avec succès !'));
        } catch(\Exception $e) {
            return new JsonResponse(array('message' => $e->getMessage()), 400);
        }
    }

    /**
     * @Route("/json/{id}", name="inck_article_bookshelf_article_get", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     * @ParamConverter("article", class="InckArticleBundle:Article")
     * @Method("GET")
     * @return JsonResponse
     */
    public function getArticleAction(Article $article)
    {
        try {

            $data = [
                'message' => 'Aucune erreur n\'est survenue !',
                "bookshelfs" => []
            ];

            foreach($this->getUser()->getBookshelfs() as $bookshelf){
                array_push($data["bookshelfs"], [
                    "id" => $bookshelf->getId(),
                    "title" => $bookshelf->getTitle(),
                    "hasArticle" => $bookshelf->hasArticle($article->getId())
                ]);
            }

            return new JsonResponse($data);
        } catch(\Exception $e) {
            return new JsonResponse(array('message' => $e->getMessage()), 400);
        }
    }

    /**
     * @Route("/{bookshelf_id}/{article_id}", name="inck_article_bookshelf_article_add", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     * @ParamConverter("bookshelf", class="InckArticleBundle:Bookshelf", options={"id" = "bookshelf_id"})
     * @ParamConverter("article", class="InckArticleBundle:Article", options={"id" = "article_id"})
     * @Method("PUT")
     * @param Bookshelf $bookshelf
     * @param Article $article
     * @return JsonResponse
     */
    public function addArticleAction(Bookshelf $bookshelf, Article $article)
    {
        try {
            if ($this->getUser() !== $bookshelf->getUser()) {
                throw $this->createAccessDeniedException("Vous n'avez pas le droit de modifier cette bibliothèque !");
            }

            $em = $this->getDoctrine()->getManager();
            $bookshelf->addArticle($article);
            $em->flush();

            return new JsonResponse(array('message' => 'Article a été ajouté avec succès !'));
        } catch(\Exception $e) {
            return new JsonResponse(array('message' => $e->getMessage()), 400);
        }
    }

    /**
     * @Route("/{bookshelf_id}/{article_id}", name="inck_article_bookshelf_article_remove", options={"expose"=true}, condition="request.isXmlHttpRequest()")
     * @ParamConverter("bookshelf", class="InckArticleBundle:Bookshelf", options={"id" = "bookshelf_id"})
     * @ParamConverter("article", class="InckArticleBundle:Article", options={"id" = "article_id"})
     * @Method("DELETE")
     * @param Bookshelf $bookshelf
     * @param Article $article
     * @return JsonResponse
     */
    public function removeArticleAction(Bookshelf $bookshelf, Article $article)
    {
        try {
            if ($this->getUser() !== $bookshelf->getUser()) {
                throw $this->createAccessDeniedException("Vous n'avez pas le droit de modifier cette bibliothèque !");
            }

            $em = $this->getDoctrine()->getManager();
            $bookshelf->removeArticle($article);
            $em->flush();

            return new JsonResponse(array('message' => 'Article a été supprimé avec succès !'));
        } catch(\Exception $e) {
            return new JsonResponse(array('message' => $e->getMessage()), 400);
        }
    }
}