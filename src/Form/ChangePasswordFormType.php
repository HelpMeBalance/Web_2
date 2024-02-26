<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;


class ChangePasswordFormType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class,
                'first_options' => [
                    'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a password',
                        ]),
                        new Length([
                            'min' => 6,
                            'minMessage' => 'Your password should be at least {{ limit }} characters',
                            // max length allowed by Symfony for security reasons
                            'max' => 4096,
                        ]),
                    ],
                    'label' => 'New password :',
                    'label_attr' => ['class' => 'text-gray-900 pr-2'],
                    'attr' => ['class' => 'p-2 rounded-md w-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent'], // Add a CSS class
                ],
                'second_options' => [
                    'label' => 'Repeat Password : ',
                    'label_attr' => ['class' => 'text-gray-900 pr-2'],
                    'attr' => ['class' => 'p-2 rounded-md w-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent'], // Add a CSS class
                ],
                'invalid_message' => 'The password fields must match.',
                'mapped' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([]);
    }
}