<?php

namespace Inck\ArticleBundle\Form\Type;

use Inck\ArticleBundle\Form\DataTransformer\TagsToNamesTransformer;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BookshelfType extends AbstractType
{

    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder ->add('title', 'text', [
            'label' => 'Titre',
            'attr'  => [
                'placeholder'   => 'Nommez votre étagère',
            ],
        ])
            ->add('share', 'checkbox',
                array('label' => 'Rendre votre étagère publique ?',
                    'required' => false,
                ))
            ->add('Description', 'textarea', [
                'label'         => 'Déscription',
                'attr'          => [
                    'placeholder'   => 'Déscription',
                    'rows'          => 4,
                    'maxlength'     => 255,
                ],])
            ->add('actions', 'form_actions', [
                'buttons' => [
                    'publish'   => [
                        'type'      => 'submit',
                        'options'   => [
                            'label' => 'Publier l\'étagère',
                            'attr'  => [
                                'class' => 'btn-success',
                                'icon'  => 'send',
                            ],
                        ]
                    ],
                    'cancel'      => [
                        'type'      => 'reset',
                        'options'   => [
                            'label' => 'Reinitialiser',
                            'attr'  => [
                                'class' => 'btn-default pull-right',
                                'icon'  => 'glyphicon glyphicon-repeat',
                            ],
                        ]
                    ],
                ]
            ])

        ;

    }

    /**
     * Returns the name of this type.
     *
     * @return string The name of this type
     */
    public function getName()
    {
        return 'inck_articlebundle_bookshelf';
    }
}