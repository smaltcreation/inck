<?php

namespace Inck\ArticleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inck\ArticleBundle\Entity\Category;

class LoadCategoryData extends AbstractFixture implements FixtureInterface
{
    public static $max = 20;
    private $names = array();

    private function initialize()
    {
        $this->names = array();

        for($i = 1; $i <= self::$max; $i++)
        {
            $this->names[] = 'Catégorie '.$i;
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
            $category->setDescription('Petite description...');

            $manager->persist($category);
            $this->addReference('category-'.$key, $category);
        }

        $manager->flush();
    }

    /**
     * {@inheritDoc}
     */
    public function getOrder()
    {
        return 1;
    }
}