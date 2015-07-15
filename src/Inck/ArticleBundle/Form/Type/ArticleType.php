<?php

namespace Inck\ArticleBundle\Form\Type;

use Inck\ArticleBundle\Form\DataTransformer\TagsToNamesTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
            ->add('anonymous', 'checkbox',
                array('label' => 'Publier Anonymement?',
                    'required' => false,
                ))
            ->add('summary', 'textarea', [
                'label'         => 'Résumé',
                'attr'          => [
                    'placeholder'   => 'Résumez le contenu de votre article',
                    'rows'          => 4,
                    'maxlength'     => 255,
                ],
            ])
            ->add('language', 'afe_select2_language', [
                'label'     => 'Langue',
                'data'      => 'fr',
            ])
            ->add('content', 'textarea', [
                'label'     => false,
            ])
            ->add('categories', 'afe_select2_entity', [
                'label'         => 'Catégories',
                'class'         => 'InckArticleBundle:Category',
                'choice_label'  => 'name',
                'multiple'      => 'multiple',
                'configs'       => [
                    'placeholder'           => 'Sélectionnez 1 à 3 catégories',
                    'maximumSelectionSize'  => 3,
                    'width'                 => 'container',
                ],
                'translation_domain' => 'ArticleBundle',
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
            ->add('official', 'checkbox', [
                'label' => 'Annonce officielle',
                'required'  => false,
            ])
            ->add('actions', 'form_actions', [
                'buttons' => [
                    'publish'   => [
                        'type'      => 'submit',
                        'options'   => [
                            'label' => 'Publier l\'article',
                            'attr'  => [
                                'class' => 'btn-success',
                                'icon'  => 'send',
                            ],
                        ]
                    ],
                    'draft'      => [
                        'type'      => 'submit',
                        'options'   => [
                            'label' => 'Enregistrer comme brouillon',
                            'attr'  => [
                                'class' => 'btn-default',
                                'icon'  => 'file',
                            ],
                        ]
                    ],
                    'cancel'      => [
                        'type'      => 'reset',
                        'options'   => [
                            'label' => 'Annuler',
                            'attr'  => [
                                'class' => 'btn-default pull-right',
                                'icon'  => 'trash',
                            ],
                        ]
                    ],
                ]
            ])
        ;
    }
    
    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
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
