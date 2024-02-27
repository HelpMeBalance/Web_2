<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\Regex;
class EditProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname', TextType::class,[
                'attr' => [
                    'class' => 'p-2 rounded-md w-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent'],
                // 'mapped' => false,
                'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a last name.',
                        ]),
                        new Length([
                            'minMessage' => 'Your last name should be at least {{ limit }} characters.',
                            'maxMessage' => 'Your last name should be no more than {{ limit }} characters.',
                            'min' => 2,
                            'max' => 50,
                        ]),
                        new Regex([
                            'pattern' => '/^[a-zA-Z]+$/',
                            'message' => 'Your last name should contain only alphabets.',
                        ]),
                    ],
            ])
            ->add('firstname', TextType::class,[
                // 'mapped' => false,
                'attr' => [
                    'class' => 'p-2 rounded-md w-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent'],
                'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a first name.',
                        ]),
                        new Length([
                            'minMessage' => 'Your first name should be at least {{ limit }} characters.',
                            'maxMessage' => 'Your first name should be no more than {{ limit }} characters.',
                            'min' => 2,
                            'max' => 50,
                        ]),
                        new Regex([
                            'pattern' => '/^[a-zA-Z]+$/',
                            'message' => 'Your first name should contain only alphabets.',
                        ]),
                    ],
            ])
            ->add('profilePictureFile', FileType::class, [
                'label' => 'Profile Picture',
                'mapped' => false, // This field is not mapped to any entity property
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}