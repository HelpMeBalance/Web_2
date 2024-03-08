<?php

// CommandeType.php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Security\Core\Security;


class CommandeType extends AbstractType
{
    private Security $security;

    public function __construct(Security $security)
    {
        $this->security = $security;
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
                'placeholder' => 'Choose payment method',
                'required' => true,
            ])
            ->add('user', ChoiceType::class, [
                'choices' => $this->getUserChoices(),
                'choice_label' => function (User $user) {
                    return $user->getFirstname() . ' ' . $user->getLastname();
                },
                'placeholder' => 'Choose an option',
            ])
            ->add('saleCode', TextType::class, [
                'label' => 'Sale Code (Optional)',
                'required' => false,
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
            return [$user->getFirstname() . ' ' . $user->getLastname() => $user];
        }

        return [];
    }
}