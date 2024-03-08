<?php

namespace App\Form;

use App\Entity\Formulaire;
use App\Entity\Question;
use App\Entity\Reponse;
use App\Repository\ReponseRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FormulaireType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $question = $options['question'];
        $builder

            ->add('Reponse', EntityType::class, [
                'class' => Reponse::class,
                'choice_label' => 'Reponse',
                'multiple' => true,
                'expanded' => true,
                'query_builder' => function (ReponseRepository $er) use ($question) {
                    return $er->createQueryBuilder('r')
                        ->andWhere('r.question = :question')
                        ->setParameter('question', $question);
                },
                'attr' => [
                    'class' => 'form-check-input',
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Formulaire::class,
        ]);
        $resolver->setRequired('question');
    }
}
