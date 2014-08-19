<?php

namespace Inck\ArticleBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

/**
 * @Route("/category")
 */
class CategoryController extends Controller
{
    /**
     * @Route("/{id}/{slug}", name="inck_article_category_show", requirements={"id" = "\d+"})
     * @Template()
     */
    public function showAction($id)
    {
        try
        {
            $em = $this->getDoctrine()->getManager();
            $category = $em->getRepository('InckArticleBundle:Category')->find($id);
            $articlesLength = $em->getRepository('InckArticleBundle:Article')->countByCategory($category->getId(), true);

            if(!$category) {
                throw new \Exception("Catégorie inexistante.");
            }

            return array(
                'category' => $category,
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
