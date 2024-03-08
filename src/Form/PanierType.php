<?php

namespace App\Form;

use App\Entity\Panier;
use App\Entity\Article;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class PanierType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
           // ->add('PrixTot')
            ->add('Quantity')
            // Assuming 'article' is the property name representing the relationship with Article entity
            ->add('article', EntityType::class, [
                'class' => Article::class,
                'choice_label' => function (Article $article) {
                    return $article->getNom() . ' - ' . $article->getPrix();
                },
                'multiple' => false,
                'expanded' => true,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Panier::class,
        ]);
    }
}
