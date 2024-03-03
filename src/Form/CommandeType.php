<?php

// CommandeType.php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;

class CommandeType extends AbstractType
{
    private Security $security;
    private UserRepository $userRepository;

    public function __construct(Security $security, UserRepository $userRepository)
    {
        $this->security = $security;
        $this->userRepository = $userRepository;
    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('username')
            ->add('address')
            ->add('paymentmethode', ChoiceType::class, [
                'choices' => [
                    'Card' => 'Card',
                    'Cash' => 'Cash',
                ],
                'placeholder' => 'Choose payment method', // Optional placeholder
                'required' => true, // Ensure a value is selected
            ])
            ->add('user', ChoiceType::class, [
                'choices' => $this->getUserChoices(),
                'choice_label' => function (User $user) {
                    // Return full name if user exists, otherwise an empty string
                    return $user->getFirstname() . ' ' . $user->getLastname();
                },
                'placeholder' => 'Choose an option',
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);
    }

    private function getUserChoices(): array
    {
        $user = $this->security->getUser();
        if ($user instanceof User) {
            // Fetch users from the repository
            $users = $this->userRepository->findAll();
            //$users = $this->userRepository->findById(); // to get the list of your user only

            // Create an array of user choices
            $choices = [];
            foreach ($users as $user) {
                $choices[$user->getFirstname() . ' ' . $user->getLastname()] = $user;
            }

            return $choices;
        }

        return [];
    }
}
