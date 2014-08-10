<?php

namespace Inck\ArticleBundle\DataFixtures\ORM;

use Doctrine\Common\DataFixtures\AbstractFixture;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inck\ArticleBundle\Entity\Tag;

class LoadTagData extends AbstractFixture implements FixtureInterface
{
    /**
     * Nombre total de tags
     */
    const MAX = 50;

    /**
     * Préfixe de la référence d'un tag
     */
    const REFERENCE_PREFIX = 'tag-';

    /**
     * Noms des tags
     * @var array
     */
    private $names;

    /**
     * Génère les noms des tags
     */
    private function initialize()
    {
        $this->names = array();

        for($i = 1; $i <= self::MAX; $i++)
        {
            $this->names[$i] = 'tag'.$i;
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
            $tag = new Tag();
            $tag->setName($name);

            $manager->persist($tag);
            $this->addReference(self::REFERENCE_PREFIX.$key, $tag);
        }

        $manager->flush();
    }
}