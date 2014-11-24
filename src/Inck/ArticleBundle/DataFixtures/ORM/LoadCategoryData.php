<?php

namespace Inck\ArticleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inck\ArticleBundle\Entity\Category;

class LoadCategoryData extends AbstractFixture implements FixtureInterface
{
    /**
     * Nombre total de catégories
     */
    const MAX = 11;

    /**
     * Préfixe de la référence d'une catégories
     */
    const REFERENCE_PREFIX = 'category-';

    /**
     * Noms des catégories
     * @var array
     */
    private $names;

    /**
     * Descriptions des catégories
     * @var array
     */
    private $descriptions;

    /**
     * Génère les noms des catégories
     */
    private function initialize()
    {
        $this->names = array(
            'politic',
            'literature',
            'philosophy',
            'society',
            'history',
            'science',
            'entertainment',
            'sport',
            'art',
            'economy',
            'health',
            'technology'
        );
        
        $this->descriptions = array(
            'category.description.politic',
            'category.description.literature',
            'category.description.philosophy',
            'category.description.society',
            'category.description.history',
            'category.description.science',
            'category.description.entertainment',
            'category.description.sport',
            'category.description.art',
            'category.description.economy',
            'category.description.health',
            'category.description.technology'
        );
    }

    /**
     * {@inheritDoc}
     */
    public function load(ObjectManager $manager)
    {
        $this->initialize();

        foreach($this->names as $key => $name)
        {
            $category = new Category();
            $category->setName($name);
            $category->setDescription($this->descriptions[$key]);

            $manager->persist($category);
            $this->addReference(self::REFERENCE_PREFIX.$key, $category);
        }

        $manager->flush();
    }
}