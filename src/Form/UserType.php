<?php

namespace App\Form;

// src/Form/UserType.php

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\CallbackTransformer;

class UserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('firstname') // Assuming 'firstname' is a property of your User entity
            ->add('lastname') // Assuming 'firstname' is a property of your User entity
            ->add('email') // Assuming 'email' is a property of your User entity
            ->add('roles', ChoiceType::class, [
                'choices' => [
                    'User' => 'ROLE_USER',
                    'Admin' => 'ROLE_ADMIN',
                    // Add other roles as needed
                ],
                'expanded' => true, // for checkboxes
    'multiple' => false, // False for radio buttons, true for checkboxes
            ]);
        ;
        $builder->get('roles')
    ->addModelTransformer(new CallbackTransformer(
        function ($rolesAsArray) {
            // transform the array to a string
            return count($rolesAsArray) ? $rolesAsArray[0] : null;
        },
        function ($rolesAsString) {
            // transform the string back to an array
            return [$rolesAsString];
        }
    ));

    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your default options here
        ]);
    }
}
