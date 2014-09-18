<?php

namespace Inck\ArticleBundle\Controller;

use Inck\ArticleBundle\Entity\ArticleRepository;
use Inck\ArticleBundle\Entity\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;

class CategoryController extends Controller
{
    /**
     * @Route("/category/{id}/{slug}", name="inck_article_category_show", requirements={"id" = "\d+"})
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

            return $this->redirect($this->generateUrl('inck_article_category_list'));
        }
    }

    /**
     * @Route("/categories", name="inck_article_category_list")
     * @Template()
     */
    public function listAction()
    {
        try
        {
            $em = $this->getDoctrine()->getManager();

            /** @var CategoryRepository $repository */
            $repository = $em->getRepository('InckArticleBundle:Category');
            $categories = $repository->getPopular();
            $categoriesLength = $repository->countAll();

            $lastPublishedArticles = array();
            /** @var ArticleRepository $repository */
            $repository = $em->getRepository('InckArticleBundle:Article');

            /** @var Category $category */
            foreach($categories as $category) {
                $lastPublishedArticles[$category->getId()] = $repository->getLastOfCategory($category->getId(), true);
            }

            return array(
                'categories' => $categories,
                'categoriesLength' => $categoriesLength,
                'lastPublishedArticles' => $lastPublishedArticles
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
