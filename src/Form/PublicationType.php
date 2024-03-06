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
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Doctrine\ORM\EntityRepository;

class PublicationType extends AbstractType
{
    private function addSousCategorieField(FormInterface $form, ?Categorie $categorie)
    {
        $form->add('SousCategorie', EntityType::class, [
            'class' => SousCategorie::class,
            'placeholder' => $categorie ? 'Please select a category first' : 'Select a subcategory',
            'choices' => $categorie ? $categorie->getSousCategories() : [],
            'choice_label' => 'nom',
            'required' => false,
        ]);
    }

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
                'placeholder' => 'Select a category',
                'mapped' => false,
            ])
            ->add('titre')
            ->add('contenu')
            ->add('comOuvert')
            ->add('anonyme')
            ->add('imageFile', FileType::class, [
                'label' => 'Publication Picture',
                'mapped' => false,
                'required' => false,
            ]);

        $builder->get('Categorie')->addEventListener(
            FormEvents::POST_SUBMIT,
            function (FormEvent $event) {
                $form = $event->getForm();
                $this->addSousCategorieField($form->getParent(), $form->getData());
            }
        );

        $builder->addEventListener(
            FormEvents::POST_SET_DATA,
            function (FormEvent $event) {
                $data = $event->getData();
                $form = $event->getForm();
                $this->addSousCategorieField($form, $data->getCategorie() ? $data->getCategorie() : null);
            }
        );
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Publication::class,
        ]);
    }
}
