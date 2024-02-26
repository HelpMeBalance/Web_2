<?php

namespace App\Form;

use App\Entity\Article;
use App\Entity\CategorieProduit;
use App\Entity\Panier;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class ArticleType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('prix')
            ->add('nom')
            ->add('description')
            ->add('panier', EntityType::class, [
                'class' => Panier::class,
                'choice_label' => 'id',
                'multiple' => true,
            ])
            ->add('categorie', EntityType::class, [
                'class' => CategorieProduit::class,
                'choice_label' => 'id',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Article::class,
        ]);
    }
}
