<?php

namespace Inck\ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

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
}
