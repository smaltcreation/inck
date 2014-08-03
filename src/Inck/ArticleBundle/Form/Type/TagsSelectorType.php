<?php

namespace Inck\ArticleBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Inck\ArticleBundle\Form\DataTransformer\TagsToNamesTransformer;
use Doctrine\Common\Persistence\ObjectManager;

class TagsSelectorType extends AbstractType
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

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $transformer = new TagsToNamesTransformer($this->om);
        $builder->addModelTransformer($transformer);
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'inck_articlebundle_tags_selector';
    }
}