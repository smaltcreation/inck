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
                'label'         => 'filter.form.categories',
                'class'         => 'InckArticleBundle:Category',
                'choice_label'  => 'name',
                'multiple'      => 'multiple',
                'required'      => false,
                'attr'          => [
                    'placeholder'   => 'filter.form.byCategories',
                ],
            ])
            ->add('tags', 'inck_articlebundle_tags_selector', [
                'label'     => 'filter.form.tags',
                'required'  => false,
                'attr'      => [
                    'placeholder'   => 'filter.form.byTags',
                ],
            ])
            ->add('authors', 'inck_userbundle_users_selector', [
                'label'     => 'filter.form.author',
                'required'  => false,
                'attr'      => [
                    'placeholder'   => 'filter.form.byAuthor',
                ],
            ])
            ->add('order', 'choice', [
                'label'         => 'filter.form.sortBy',
                'required'      => false,
                'placeholder'   => false,
                'choices'       => [
                    'date'  => 'filter.form.publication_date',
                    'vote'  => 'filter.form.popularity',
                ],
            ])
            ->add('popularity', 'afe_select2_choice', [
                'label'         => 'filter.form.popularity',
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
                            'label' => 'filter.form.filter',
                            'attr'  => [
                                'class' => 'btn-success',
                                'icon'  => 'filter',
                            ],
                        ],
                    ],
                    'cancel'    => [
                        'type'      => 'reset',
                        'options'   => [
                            'label' => 'filter.form.cancel',
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
            'translation_domain' => 'ArticleBundle',
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
