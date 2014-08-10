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
    const MAX = 50;

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
     * Génère les noms des catégories
     */
    private function initialize()
    {
        $this->names = array();

        for($i = 1; $i <= self::MAX; $i++)
        {
            $this->names[$i] = 'Catégorie '.$i;
        }
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
            $category->setDescription('Description de la catégorie...');

            $manager->persist($category);
            $this->addReference(self::REFERENCE_PREFIX.$key, $category);
        }

        $manager->flush();
    }
}