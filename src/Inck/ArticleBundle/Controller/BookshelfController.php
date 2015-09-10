<?php

namespace Inck\ArticleBundle\Controller;

use Inck\ArticleBundle\Entity\Bookshelf;
use Inck\ArticleBundle\Form\Type\BookshelfType;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Method;
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
 */
class BookshelfController extends Controller
{
    /**
     * @Route("/show/{id}", name="inck_article_bookshelf_show", requirements={"id"})
     * @ParamConverter("bookshelf", options={"mapping": {"id": "id"}})
     * @Template()
     * @param Bookshelf $bookshelf
     * @return array
     */
    public function showAction(Bookshelf $bookshelf)
    {
        return array(
            'bookshelf' => $bookshelf,
        );
    }

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
     * @Route("/edit/{id}", name="inck_article_bookshelf_edit")
     * @ParamConverter("bookshelf", options={"mapping": {"id": "id"}})
     * @Secure(roles="ROLE_USER")
     * @param Request $request
     * @return Response
     */
    public function editAction(Request $request, Bookshelf $bookshelf)
    {
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

            $this->get('session')->getFlashBag()->add('success', 'L\'étagère a bien été enregistrée !');

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
     * @Route("/delete/{id}", name="inck_article_bookshelf_delete", requirements={"id" = "\d+"}, options={"expose"=true}, condition="request.isXmlHttpRequest()")
     * @Method("DELETE")
     * @ParamConverter("bookshelf", options={"mapping": {"id": "id"}})
     * @Secure(roles="ROLE_USER")
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
}