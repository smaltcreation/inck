<?php

namespace Inck\ArticleBundle\Form\Type;

use Inck\ArticleBundle\Form\DataTransformer\TagsToNamesTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ArticleType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title', 'text', ['label' => 'Titre'])
            ->add('summary', 'text', ['label' => 'Résumé'])
            ->add('content', 'ckeditor', ['label' => 'Contenu'])
            ->add('categories', 'afe_select2_entity', [
                'label'     => 'Catégories',
                'class'     => 'InckArticleBundle:Category',
                'property'  => 'name',
                'multiple'  => 'multiple',
                'configs'      => [
                    'placeholder'           => 'Sélectionnez 1 a 3 catégories',
                    'maximumSelectionSize'  => 3,
                ],
            ])
            ->add('tags', 'inck_articlebundle_tags_selector', [
                'label' => 'Tags',
                'attr'  => [
                    'placeholder'   => 'Ajoutez des tags',
                ],
            ])
            ->add('image', new ImageType(), ['label' => false])
            ->add('actions', 'form_actions', [
                'buttons' => [
                    'publish'   => [
                        'type'      => 'submit',
                        'options'   => [
                            'label' => 'Publier',
                            'attr'  => ['class' => 'btn-success'],
                        ]
                    ],
                    'draft'      => [
                        'type'      => 'submit',
                        'options'   => [
                            'label' => 'Enregistrer comme brouillon',
                            'attr'  => ['class' => 'btn-primary'],
                        ]
                    ],
                    'cancel'      => [
                        'type'      => 'reset',
                        'options'   => [
                            'label' => 'Annuler',
                            'attr'  => ['class' => 'btn-danger'],
                        ]
                    ],
                ]
            ])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults([
            'data_class' => 'Inck\ArticleBundle\Entity\Article'
        ]);
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'inck_articlebundle_article';
    }
}
