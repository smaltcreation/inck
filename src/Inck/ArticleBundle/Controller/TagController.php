<?php

namespace Inck\ArticleBundle\Controller;

use Inck\ArticleBundle\Entity\Tag;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\JsonResponse;

class TagController extends Controller
{
    /**
     * @Route("/tag/autocomplete/{mode}/{name}", name="inck_article_tag_autocomplete", requirements={"mode" = "id|name"}, options={"expose"=true})
     */
    public function autocomplete($mode, $name)
    {
        // Récupération des tags
        $em = $this->getDoctrine()->getManager();
        $tags = $em->getRepository('InckArticleBundle:Tag')->getAutocompleteResults($name) ?: array();

        // Création du tableau utilisé par Select2
        $results = array();

        foreach($tags as $tag) {
            $results[] = array(
                'id'    => $tag[$mode],
                'text'  => $tag['name'],
            );
        }

        // Renvoi des données
        return new JsonResponse(array(
            'results'   => $results,
        ));
    }

    /**
     * @Route("/tag/{id}/{slug}", name="inck_article_tag_show", requirements={"id" = "\d+"})
     * @Template()
     */
    public function showAction($id, $slug)
    {
        try {
            $em = $this->getDoctrine()->getManager();
            /** @var Tag $tag */
            $tag = $em->getRepository('InckArticleBundle:Tag')->findOneBy(array('id' => $id, 'slug' => $slug));
            $articlesLength = $em->getRepository('InckArticleBundle:Article')->countByTag($tag->getId(), true);

            if(!$tag) {
                throw new \Exception("Tag inexistant.");
            }

            return array(
                'tag' => $tag,
                'articlesLength' => $articlesLength
            );
        } catch(\Exception $e) {
            $this->get('session')->getFlashBag()->add(
                'danger',
                $e->getMessage()
            );

            return $this->redirect($this->generateUrl('home'));
        }
    }
}
