<?php

namespace Inck\UserBundle\Form\Type;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use FOS\UserBundle\Form\Type\ProfileFormType as BaseType;

class ProfileFormType extends BaseType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        //parent::buildForm($builder, $options);

        $builder
            ->add('firstname', 'text', array(
            'required'  => false, 'attr' => array('placeholder' => 'Anakin'),
        ))
            ->add('lastname', 'text', array(
            'required'  => false, 'attr' => array('placeholder' => 'Skywalker'),
        ))
            ->add('birthdate', 'afe_date_picker', array(
            'required'  => false,
        ))
            ->add('address', 'text', array(
            'required'  => false, 'attr' => array('placeholder' => 'Somewhere'),
        ))
            ->add('zipcode', 'text', array(
            'required'  => false, 'attr' => array('placeholder' => '69000'),
        ))
            ->add('city', 'text', array(
            'required'  => false, 'attr' => array('placeholder' => 'Tatooine'),
        ))
            ->add('country', 'afe_select2_country', array(
            'required'  => false, 'data' => 'FR', 'attr' => array('placeholder' => 'Arkanis sector'),
        ))
            ->add('school', 'text', array(
            'required'  => false, 'attr' => array('placeholder' => 'Jedi Institut'),
        ))
            ->add('enabled', 'checkbox', array(
            'required'  => true, 'attr' => array('style' => 'display: none;'), 'label' => false,
        ))
        ;
    }

    /**
     * @param OptionsResolverInterface $resolver
     */
    public function setDefaultOptions(OptionsResolverInterface $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Inck\UserBundle\Entity\User',
        ));
    }

    /**
     * @return string
     */
    public function getName()
    {
        return 'inck_user_profile';
    }
}