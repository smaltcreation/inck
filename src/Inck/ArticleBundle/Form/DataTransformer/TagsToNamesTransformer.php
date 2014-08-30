<?php

namespace Inck\ArticleBundle\Form\DataTransformer;

use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Form\DataTransformerInterface;
use Doctrine\Common\Persistence\ObjectManager;
use Inck\ArticleBundle\Entity\Tag;

class TagsToNamesTransformer implements DataTransformerInterface
{
    /**
     * @var ObjectManager
     */
    private $om;

    /**
     * @param ObjectManager $om
     */
    public function __construct(ObjectManager $om)
    {
        $this->om = $om;
    }

    /**
     * Transforms an ArrayCollection (tags) to a string (names).
     *
     * @param  ArrayCollection|null $tags
     * @return string
     */
    public function transform($tags)
    {
        if (!$tags) {
            return '';
        }

        // Récupération des noms
        $names = array();

        /** @var Tag $tag */
        foreach($tags as $tag)
        {
            $names[] = $tag->getName();
        }

        return implode(',', $names);
    }

    /**
     * Transforms a string (names) to an ArrayCollection (tags).
     *
     * @param  string $tags
     * @return ArrayCollection|null
     */
    public function reverseTransform($tags)
    {
        if (!$tags) {
            return new ArrayCollection();
        }

        $tags = explode(',', $tags);

        // Récupération des tags existants
        $tags = $this->om
            ->getRepository('InckArticleBundle:Tag')
            ->findWhereNameIn($tags)
        ;

        // Recherche de nouveaux tags
        $newNames = array();

        foreach($tags as $name)
        {
            $isNew = true;

            /** @var Tag $tag */
            foreach($tags as $tag)
            {
                if($name === $tag->getName())
                {
                    $isNew = false;
                    break;
                }
            }

            if($isNew)
            {
                $newNames[] = $name;
            }
        }

        // Création des nouveaux tags
        foreach($newNames as $newName)
        {
            $tag = new Tag();
            $tag->setName($newName);
            $tags[] = $tag;
        }

        return $tags;
    }
}