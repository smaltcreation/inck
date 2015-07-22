<?php

namespace Inck\ArticleBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

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
                'label'         => 'Catégories',
                'class'         => 'InckArticleBundle:Category',
                'choice_label'  => 'name',
                'multiple'      => 'multiple',
                'required'      => false,
                'attr'          => [
                    'placeholder'   => 'Filtrer par catégories',
                ],
                'translation_domain' => 'ArticleBundle',
            ])
            ->add('tags', 'inck_articlebundle_tags_selector', [
                'label'     => 'Tags',
                'required'  => false,
                'attr'      => [
                    'placeholder'   => 'Filtrer par tags',
                ],
            ])
            ->add('authors', 'inck_userbundle_users_selector', [
                'label'     => 'Auteurs',
                'required'  => false,
                'attr'      => [
                    'placeholder'   => 'Filtrer par auteur',
                ],
            ])
            ->add('order', 'choice', [
                'label'         => 'Trier par',
                'required'      => false,
                'placeholder'   => false,
                'choices'       => [
                    'date'  => 'Date de publication',
                    'vote'  => 'Popularité',
                ],
            ])
            ->add('popularity', 'afe_select2_choice', [
                'label'         => 'Popularité',
                'required'      => false,
                'choices'       => [
                    'hot'       => 'Hot',
                    'trending'  => 'Trending',
                    'fresh'     => 'Fresh',
                ],
                'multiple'      => true,
            ])
            ->add('search', 'hidden')
            ->add('type', 'hidden')
            ->add('actions', 'form_actions', [
                'buttons' => [
                    'filter'    => [
                        'type'      => 'submit',
                        'options'   => [
                            'label' => 'Filtrer',
                            'attr'  => [
                                'class' => 'btn-success',
                                'icon'  => 'filter',
                            ],
                        ],
                    ],
                    'cancel'    => [
                        'type'      => 'reset',
                        'options'   => [
                            'label' => 'Annuler',
                            'attr'  => [
                                'class' => 'btn-primary',
                                'icon'  => 'refresh',
                            ],
                        ],
                    ],
                ],
            ])
        ;
    }

    /**
     * @param OptionsResolver $resolver
     */
    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'categories'    => array(),
            'tags'          => array(),
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
