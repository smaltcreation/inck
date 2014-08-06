<?php

namespace Inck\CoreBundle\Form\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints\Email;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class ContactType extends AbstractType
{
    /**
     * @param FormBuilderInterface $builder
     * @param array $options
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder

            ->add('email', 'email', [
                'label' => 'Mon email',
                'constraints' => [
                    new NotBlank(),
                    new Email(),
                ],
            ])
            ->add('subject', 'text', [
                'label' => 'Objet',
                'constraints'      => [
                    new Length(array('max' => 100)),
                    new NotBlank(),
                ],
            ])
            ->add('content',
                'textarea', [
                'label' => 'Corps',
                'constraints' => [
                    new Length(array('min' => 20, 'max' => 500)),
                    new NotBlank(),
                ],
                'attr' => [
                    'rows' => 9
                ]
            ]);
    }

    public function getName()
    {
        return 'inck_corebundle_contact';
    }
}