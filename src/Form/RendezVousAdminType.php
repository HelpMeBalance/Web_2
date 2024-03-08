<?php

namespace App\Form;

use App\Entity\RendezVous;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;

class RendezVousAdminType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateR',DateTimeType::class, [
                'widget' => 'single_text',
                'html5' => true,
                'attr' => [
                    'type' => 'datetime-local',
                ],
            ])
            ->add('nomService', ChoiceType::class, [
                'choices' => [
                    'Individuel' => 'Individuel',
                    'Couple' => 'Couple',
                    'Child' => 'Child',
                ]
            ])
            ->add('statut')
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstname',
                'query_builder' => function (UserRepository $repository) {
                    $queryBuilder = $repository->createQueryBuilder('u')
                        ->select('u')
                        ->where('u.roles LIKE :role')
                        ->setParameter('role', "%psy%")
                    ;
                    return $queryBuilder;
                }
                ])
            ->add('patient', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'firstname'])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => RendezVous::class,
        ]);
    }
}
