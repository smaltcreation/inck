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
            ->add('title', 'text', array('label' => 'Titre'))
            ->add('icone', 'text')
            ->add('color', 'afe_mini_colors', array('label' => 'Couleur de fond'))
            ->add('colorText', 'afe_mini_colors', array('label' => 'Couleur du texte'))
            ->add('description')
            ->add('lvl', 'integer', array('label' => 'Importance'))
            ->add('users', 'afe_select2_entity', array(
                'class' => 'InckUserBundle:User',
                'choice_label' => 'username',
                'multiple' => 'true'))
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