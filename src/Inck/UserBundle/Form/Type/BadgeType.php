<?php

namespace Inck\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BadgeType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('title')
            ->add('color')
            ->add('description')
            ->add('createdAt')
            ->add('users')
            ->add('imageFile', 'afe_single_upload', [
                'label'         => 'Image de prévisualisation',
                'deleteable'    => 'imageName',
                'required'      => false,
            ])
        ;
    }
    
    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Inck\UserBundle\Entity\Badge'
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'inck_userbundle_badge';
    }
}