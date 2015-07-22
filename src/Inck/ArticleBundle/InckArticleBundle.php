<?php

namespace Inck\ArticleBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;
use Presta\SitemapBundle\Event\SitemapPopulateEvent;
use Presta\SitemapBundle\Sitemap\Url\UrlConcrete;
use Inck\ArticleBundle\Entity\Article;
use Inck\ArticleBundle\Entity\Category;
use Inck\ArticleBundle\Entity\Tag;

class InckArticleBundle extends Bundle
{
    public function boot()
    {
        $router = $this->container->get('router');
        $event  = $this->container->get('event_dispatcher');

        //listen presta_sitemap.populate event
        $event->addListener(
            SitemapPopulateEvent::ON_SITEMAP_POPULATE,
            function(SitemapPopulateEvent $event) use ($router){

                //Get all published articles, tags and categories
                $em = $this->container->get('doctrine')->getManager();

                $repository = $this->container->get('inck_article.repository.article_repository');
                $articles = $repository->findByFilters(array(
                    'type' => 'published',
                ), false, 1);

                $repository = $em->getRepository('InckArticleBundle:Category');
                $categories = $repository->findAll();

                $repository = $em->getRepository('InckArticleBundle:Tag');
                $tags = $repository->findAll();

                if($articles) {
                    foreach($articles as $article) {
                        $url = $router->generate('inck_article_article_show', array(
                            'id' => $article->getId(),
                            'slug' => $article->getSlug(),
                        ), true);

                        $this->addUrl($event, $url);
                    }
                }

                if($categories) {
                    foreach($categories as $category) {
                        $url = $router->generate('inck_article_category_show', array(
                            'id' => $category->getId(),
                            'slug' => $category->getSlug(),
                        ), true);

                        $this->addUrl($event, $url);
                    }
                }

                if($tags) {
                    foreach($tags as $tag) {
                        $url = $router->generate('inck_article_tag_show', array(
                            'id' => $tag->getId(),
                            'slug' => $tag->getSlug(),
                        ), true);

                        $this->addUrl($event, $url);
                    }
                }

            });
    }

    private function addUrl($event, $url)
    {
        $event->getGenerator()->addUrl(
            new UrlConcrete(
                $url,
                new \DateTime(),
                UrlConcrete::CHANGEFREQ_HOURLY,
                1
            ),
            'default'
        );
    }
}
