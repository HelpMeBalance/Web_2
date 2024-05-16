<?php

namespace App\Form;

use App\Entity\Consultation;
use App\Entity\RendezVous;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class RatingType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('idrv', IntegerType::class, [
                'label' => 'RendezvousId',
            ])
            ->add('rating', IntegerType::class, [
                'label' => 'AvisPatient',
                'constraints' => [
                    new Assert\NotBlank(['message' => 'Please enter avisPatient']),
                    new Assert\Range([
                        'min' => 0,
                        'max' => 5,
                        'notInRangeMessage' => 'Please enter a number between 0 and 5',
                    ]),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            
        ]);
    }
}
