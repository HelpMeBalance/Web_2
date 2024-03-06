<?php

namespace App\Form;

use App\Entity\Consultation;
use App\Entity\RendezVous;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use App\Entity\User;
class ConsultationType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            // ->add('duree')
            ->add('Patient', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstname',
                ])
            ->add('note')
            ->add('avisPatient')
            ->add('RecommandationSuivi')
            ->add('Psychiatre', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstname',
                ])
            ->add('rendezvous', EntityType::class, [
                'class' => RendezVous::class,
                'choice_label' => 'id',
                ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Consultation::class,
        ]);
    }
}
