<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\CategorieProduit;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;
use Symfony\Component\Validator\Constraints\File;
use Symfony\Component\Validator\Constraints\Length;
use Symfony\Component\Validator\Constraints\NotBlank;
use Symfony\Component\Validator\Constraints\Regex;


class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prix', null, [
                'attr' => [
                    'class' => 'p-2 rounded-md w-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a price.',
                    ]),
                    new Length([
                        'minMessage' => 'Your price should be at least {{ limit }} characters.',
                        'maxMessage' => 'Your price should be no more than {{ limit }} characters.',
                        'min' => 1,
                        'max' => 50,
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9]+$/',
                        'message' => 'Your price should contain only numbers.',
                    ]),
                ],
            ])
            ->add('quantite', null, [
                'attr' => [
                    'class' => 'p-2 rounded-md w-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a quantity.',
                    ]),
                    new Length([
                        'minMessage' => 'Your quantity should be at least {{ limit }} characters.',
                        'maxMessage' => 'Your quantity should be no more than {{ limit }} characters.',
                        'min' => 1,
                        'max' => 50,
                    ]),
                    new Regex([
                        'pattern' => '/^[0-9]+$/',
                        'message' => 'Your quantity should contain only numbers.',
                    ]),
                ],
            ])
            ->add('nom', null, [
                'attr' => [
                    'class' => 'p-2 rounded-md w-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a name.',
                    ]),
                    new Length([
                        'minMessage' => 'Your name should be at least {{ limit }} characters.',
                        'maxMessage' => 'Your name should be no more than {{ limit }} characters.',
                        'min' => 2,
                        'max' => 50,
                    ]),
                    new Regex([
                        'pattern' => '/^[a-zA-Z]+$/',
                        'message' => 'Your name should contain only alphabets.',
                    ]),
                ],
            ])
            ->add('description', null, [
                'attr' => [
                    'class' => 'p-2 rounded-md w-full border border-gray-300 focus:outline-none focus:ring-2 focus:ring-blue-600 focus:border-transparent'
                ],
                'constraints' => [
                    new NotBlank([
                        'message' => 'Please enter a description.',
                    ]),
                    new Length([
                        'minMessage' => 'Your description should be at least {{ limit }} characters.',
                        'maxMessage' => 'Your description should be no more than {{ limit }} characters.',
                        'min' => 2,
                        'max' => 50,
                    ]),
                ],
            ])
            ->add('categorie', EntityType::class, [
                'class' => CategorieProduit::class,
                'choice_label' => 'nom',
            ])
            ->add('articlePictureFile', FileType::class, [
                'label' => 'Profile Picture',
                'mapped' => false, // This field is not mapped to any entity property
                'required' => false,

        ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
