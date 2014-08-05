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
                'required'  => false,
                'label' => 'form.firstname',
                'translation_domain' => 'FOSUserBundle',
                'attr' => array('placeholder' => 'Anakin')
            ))
            ->add('lastname', 'text', array(
                'required'  => false,
                'label' => 'form.lastname',
                'translation_domain' => 'FOSUserBundle',
                'attr' => array('placeholder' => 'Skywalker')
            ))
            ->add('birthday', 'afe_date_picker', array(
                'required'  => false,
                'label' => 'form.birthday',
                'translation_domain' => 'FOSUserBundle',
                'attr' => array('placeholder' => '42 BBY')
            ))
            ->add('occupation', 'text', array(
                'required'  => false,
                'label' => 'form.occupation',
                'translation_domain' => 'FOSUserBundle',
                'attr' => array('placeholder' => 'Jedi/Sith')
            ))
            ->add('biography', 'textarea', array(
                'required'  => false,
                'label' => 'form.biography',
                'translation_domain' => 'FOSUserBundle',
                'attr' => array('placeholder' => 'Someday I will be the most powerful Jedi ever...', 'rows' => 6)
            ))
            ->add('address', 'text', array(
                'required'  => false,
                'label' => 'form.address',
                'translation_domain' => 'FOSUserBundle',
                'attr' => array('placeholder' => 'Somewhere')
            ))
            ->add('zipcode', 'text', array(
                'required'  => false,
                'label' => 'form.zipcode',
                'translation_domain' => 'FOSUserBundle',
                'attr' => array('placeholder' => '3200')
            ))
            ->add('city', 'text', array(
                'required'  => false,
                'label' => 'form.city',
                'translation_domain' => 'FOSUserBundle',
                'attr' => array('placeholder' => 'Tatooine')
            ))
            ->add('country', 'afe_select2_country', array(
                'required'  => false,
                'data' => 'FR',
                'label' => 'form.country',
                'translation_domain' => 'FOSUserBundle',
                'attr' => array('placeholder' => 'Tunisie')
            ))
            ->add('school', 'text', array(
                'required'  => false,
                'label' => 'form.school',
                'translation_domain' => 'FOSUserBundle',
                'attr' => array('placeholder' => 'Jedi Institut')
            ))
            ->add('enabled', 'checkbox', array(
                'required'  => true,
                'label' => false,
                'attr' => array('style' => 'display: none;')
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