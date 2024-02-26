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

class EditProfileType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('lastname', TextType::class,[
                'attr' => [
                    'class' => 'p-2 rounded-md w-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent'],
                'mapped' => false,
                'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a lastname',
                        ]),
                        new Length([
                            'minMessage' => 'Your lastname should be at least {{ limit }} characters',
                            'maxMessage' => 'Your lastname should be no more than {{ limit }} characters',
                            'min' => 2,
                            'max' => 50,
                        ]),
                    ],
            ])
            ->add('firstname', TextType::class,[
                'mapped' => false,
                'attr' => [
                    'class' => 'p-2 rounded-md w-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent'],
                'constraints' => [
                        new NotBlank([
                            'message' => 'Please enter a firstname',
                        ]),
                        new Length([
                            'minMessage' => 'Your firstname should be at least {{ limit }} characters',
                            'maxMessage' => 'Your firstname should be no more than {{ limit }} characters',
                            'min' => 2,
                            'max' => 50,
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