<?php

/*
 * This file is part of the FOSUserBundle package.
 *
 * (c) FriendsOfSymfony <http://friendsofsymfony.github.com/>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Inck\UserBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use FOS\UserBundle\Form\Type\GroupFormType as BaseType;

class GroupFormType extends BaseType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder, $options);

        $builder
            ->add('email', 'text', array(
                'label' => 'form.email',
                'translation_domain' => 'FOSUserBundle',
                'required'  => false,
                'attr' => array('placeholder' => 'mon@entreprise.com')
            ))
            ->add('website', 'text', array(
                'label' => 'form.website',
                'translation_domain' => 'FOSUserBundle',
                'required'  => false,
                'attr' => array('placeholder' => 'example.com')
            ))
            ->add('type', 'choice', array(
                'label' => 'form.type',
                'translation_domain' => 'FOSUserBundle',
                'choices'   => array(
                    '0' => 'Défault - Particulier',
                    '1' => 'Société',
                ),
                'expanded'  => false,
                'multiple'  => false,
            ))
            ->add('private', 'choice', array(
                'label' => 'form.private',
                'translation_domain' => 'FOSUserBundle',
                'choices'   => array(
                    '0' => 'Page publique',
                    '1' => 'Page privée',
                ),
                'expanded'  => false,
                'multiple'  => false,
            ))
            ->add('address', 'text', array(
                'required'  => false,
                'label' => 'form.address',
                'translation_domain' => 'FOSUserBundle',
                'attr' => array('placeholder' => '42 Rue Douglas Adams')
            ))
            ->add('zipcode', 'text', array(
                'required'  => false,
                'label' => 'form.zipcode',
                'translation_domain' => 'FOSUserBundle',
                'attr' => array('placeholder' => '42000')
            ))
            ->add('city', 'text', array(
                'required'  => false,
                'label' => 'form.city',
                'translation_domain' => 'FOSUserBundle',
                'attr' => array('placeholder' => 'Saint Étienne')
            ))
            ->add('country', 'afe_select2_country', array(
                'required'  => false,
                'data' => 'FR',
                'label' => 'form.country',
                'translation_domain' => 'FOSUserBundle',
                'attr' => array('placeholder' => 'France'),
                'configs'      => [
                    'width' => 'container',
                ],
            ))
            ->add('enabled', 'checkbox', array(
                'required'  => true,
                'label' => false,
                'attr' => array('style' => 'display: none;')
            ))
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => 'Inck\UserBundle\Entity\Group',
            'intention'  => 'group',
        ));
    }

    public function getName()
    {
        return 'inck_user_group';
    }
}
