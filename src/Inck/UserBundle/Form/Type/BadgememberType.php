<?php

namespace Inck\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class BadgememberType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('users', 'afe_select2_entity', array(
                'class' => 'InckUserBundle:User',
                'choice_label' => 'username',
                'multiple' => 'true'))
            ->add('submit', 'submit', array('label' => 'Mettre a jour'))
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
        return 'inck_userbundle_badge_member';
    }
}