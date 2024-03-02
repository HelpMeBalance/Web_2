<?php

namespace App\Form;

use App\Entity\Categorie;
use App\Entity\Publication;
use App\Entity\SousCategorie;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\FileType;


class PublicationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('User', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'id',
                ])
            ->add('Categorie', EntityType::class, [
                'class' => Categorie::class,
                'choice_label' => 'nom',
                 ])
            ->add('SousCategorie', EntityType::class, [
                'class' => SousCategorie::class,
                'choice_label' => 'nom',
                ])
            ->add('titre')
            ->add('contenu')
            ->add('comOuvert')
            ->add('anonyme')

            ->add('imageFile', FileType::class, [
                'label' => 'Publication Picture',
                'mapped' => false, // This field is not mapped to any entity property
                'required' => false,
            ])
            ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publication::class,
        ]);
    }
}
    