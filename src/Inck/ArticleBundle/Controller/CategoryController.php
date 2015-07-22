<?php

namespace Inck\ArticleBundle\Controller;

use Inck\ArticleBundle\Entity\ArticleRepository;
use Inck\ArticleBundle\Entity\Category;
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
    public function showAction($id, $slug)
    {
        try
        {
            $em = $this->getDoctrine()->getManager();
            $category = $em->getRepository('InckArticleBundle:Category')->findOneBy(array('id' => $id, 'slug' => $slug));

            if(!$category) {
                throw new \Exception("Catégorie inexistante.");
            }

            $articlesLength = $this->get('inck_article.repository.article_repository')->countByCategory($category->getId(), true);

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
     * @Route("/categories", name="inck_article_category_list", options={"sitemap" = true})
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

            $articlesLength =  array();
            $lastPublishedArticles = array();

            if($categories) {
                /** @var ArticleRepository $repository */
                $repository = $this->get('inck_article.repository.article_repository');

                /** @var Category $category */
                foreach($categories as $category) {
                    $i = $category->getId();
                    $articlesLength[$i] = $repository->countByCategory($i, true);

                    if($repository->getLastOfCategory($i, true)) {
                        $lastPublishedArticles[$i] = $repository->getLastOfCategory($i, true)[0];
                    }
                    else {
                        $lastPublishedArticles[$i] = false;
                    }
                }
            }

            return array(
                'categories' => $categories,
                'categoriesLength' => count($categories),
                'lastPublishedArticles' => $lastPublishedArticles,
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
