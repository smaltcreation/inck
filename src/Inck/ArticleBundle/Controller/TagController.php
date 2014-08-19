<?php

namespace Inck\ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class TagController extends Controller
{
    /**
     * @Route("/tag/autocomplete/{name}", name="inck_article_tag_autocomplete", options={"expose"=true})
     */
    public function autocomplete($name)
    {
        // Récupération des tags
        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository('InckArticleBundle:Tag')->getAutocompleteResults($name) ?: array();

        // Création du tableau utilisé par Select2
        $results = array();

        foreach($tags as $tag)
        {
            $results[] = array(
                'id'    => $tag['name'],
                'text'  => $tag['name'],
            );
        }

        // Renvoie des données
        return new JsonResponse(array(
            'results'   => $results,
        ));
    }

    /**
     * @Route("/tag/{id}/{slug}", name="inck_article_tag_show", requirements={"id" = "\d+"})
     * @Template()
     */
    public function showAction($id)
    {
        try
        {
            $em = $this->getDoctrine()->getManager();
            $tag = $em->getRepository('InckArticleBundle:Tag')->find($id);
            $articlesLength = $em->getRepository('InckArticleBundle:Article')->countByTag($tag->getId(), true);

            if(!$tag) {
                throw new \Exception("Tag inexistante.");
            }

            return array(
                'tag' => $tag,
                'articlesLength' => $articlesLength
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
