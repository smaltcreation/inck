<?php

namespace Inck\ArticleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inck\ArticleBundle\Entity\Article;
use Inck\ArticleBundle\Entity\Category;
use Inck\ArticleBundle\Entity\Tag;
use Inck\UserBundle\DataFixtures\ORM\LoadUserData;
use Inck\UserBundle\Entity\User;

class LoadArticleData extends AbstractFixture implements FixtureInterface, DependentFixtureInterface
{
    /**
     * Nombre total d'articles
     */
    const MAX = 100;

    /**
     * Préfixe de la référence d'un article
     */
    const REFERENCE_PREFIX = 'article-';

    /**
     * Titres des articles
     * @var array
     */
    private $titles;

    /**
     * Génère les titres des articles
     */
    private function initialize()
    {
        $this->titles = array();

        for($i = 1; $i <= self::MAX; $i++)
        {
            $this->titles[$i] = 'Article '.$i;
        }
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->initialize();

        foreach($this->titles as $key => $title)
        {
            $article = new Article();
            $article->setTitle($title);
            $article->setSummary("Résumé de l'article...");

            // État
            $article->setPublished(boolval(rand(0, 1)));

            if($article->getPublished())
            {
                $article->getPublishedAt(new \DateTime());
                $article->setAsDraft(false);
            }

            else
            {
                $article->setAsDraft(boolval(rand(0, 1)));
            }

            if(!$article->getAsDraft())
            {
                $article->setPostedAt(new \DateTime());
            }

            // Contenu aléatoire
            $content = '';
            $words = rand(800, 2000);
            for($i = 0; $i < $words; $i++)
            {
                $word = range('a', 'z');
                shuffle($word);
                $word = substr(implode($word), 0, rand(1, 10));
                $content .= $word.' ';
            }

            $article->setContent($content);

            // Auteur
            /** @var $author User */
            $author = $this->getReference(LoadUserData::REFERENCE_PREFIX.rand(1, LoadUserData::MAX));
            $article->setAuthor($author);

            // Catégories
            $totalCategories = rand(1, 3);
            $categories = array();

            for($i = 0; $i < $totalCategories; $i++)
            {
                do
                {
                    $id = rand(1, LoadCategoryData::MAX);
                }
                while(in_array($id, $categories));
                $categories[] = $id;

                /** @var $category Category */
                $category = $this->getReference(LoadCategoryData::REFERENCE_PREFIX.$id);
                $article->addCategory($category);
            }

            // Tags
            $totalTags = rand(1, 10);
            $tags = array();

            for($i = 0; $i < $totalTags; $i++)
            {
                do
                {
                    $id = rand(1, LoadTagData::MAX);
                }
                while(in_array($id, $tags));
                $tags[] = $id;

                /** @var $tag Tag */
                $tag = $this->getReference(LoadTagData::REFERENCE_PREFIX.$id);
                $article->addTag($tag);
            }

            // Enregistrement
            $manager->persist($article);
            $this->addReference(self::REFERENCE_PREFIX.$key, $article);
        }

        $manager->flush();
    }

    public function getDependencies()
    {
        return array(
            'Inck\ArticleBundle\DataFixtures\ORM\LoadCategoryData',
            'Inck\ArticleBundle\DataFixtures\ORM\LoadTagData',
        );
    }
}