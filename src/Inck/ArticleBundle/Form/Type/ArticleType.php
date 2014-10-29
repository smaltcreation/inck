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
            ->add('title', 'text', [
                'label' => 'Titre',
                'attr'  => [
                    'placeholder'   => 'Ajoutez un titre clair et succinct',
                ],
            ])
            ->add('summary', 'textarea', [
                'label'         => 'Résumé',
                'attr'          => [
                    'placeholder'   => 'Résumez le contenu de votre article',
                    'rows'          => 6,
                    'maxlength'     => 255,
                ],
            ])
            ->add('content', 'ckeditor', [
                'label'     => false,
            ])
            ->add('categories', 'afe_select2_entity', [
                'label'     => 'Catégories',
                'class'     => 'InckArticleBundle:Category',
                'property'  => 'name',
                'multiple'  => 'multiple',
                'configs'      => [
                    'placeholder'           => 'Sélectionnez 1 à 3 catégories',
                    'maximumSelectionSize'  => 3,
                    'width'                 => 'container',
                ],
            ])
            ->add('tags', 'inck_articlebundle_tags_selector', [
                'label' => 'Tags',
                'attr'  => [
                    'placeholder'   => 'Ajoutez des tags',
                ],
            ])
            ->add('imageFile', 'afe_single_upload', [
                'label'         => 'Image de prévisualisation',
                'deleteable'    => 'imageName',
                'required'      => false,
            ])
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
                            'attr'  => ['class' => 'btn-default'],
                        ]
                    ],
                    'cancel'      => [
                        'type'      => 'reset',
                        'options'   => [
                            'label' => 'Annuler',
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
