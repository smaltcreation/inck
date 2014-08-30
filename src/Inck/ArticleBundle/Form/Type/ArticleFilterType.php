<?php

namespace Inck\ArticleBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticleFilterType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('categories', 'entity', [
                'label'     => 'Catégories',
                'class'     => 'InckArticleBundle:Category',
                'property'  => 'name',
                'multiple'  => 'multiple',
                'required'  => false,
                'attr'      => [
                    'placeholder'   => 'Filtrer par catégories',
                ],
            ])
            ->add('tags', 'inck_articlebundle_tags_selector', [
                'label' => 'Tags',
                'required'  => false,
                'attr'  => [
                    'placeholder'   => 'Filter par tags',
                ],
            ])
            ->add('actions', 'form_actions', [
                'buttons' => [
                    'filter'    => [
                        'type'      => 'submit',
                        'options'   => [
                            'label' => 'Filtrer',
                            'attr'  => ['class' => 'btn-success'],
                        ],
                    ],
                    'cancel'    => [
                        'type'      => 'reset',
                        'options'   => [
                            'label' => 'Annuler',
                            'attr'  => ['class' => 'btn-primary'],
                        ],
                    ],
                ],
            ])
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'categories'    => null,
            'tags'          => null,
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'inck_articlebundle_articlefilter';
    }
}
