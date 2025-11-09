<?php

namespace App\Form;

use App\Entity\Cart;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateType;

class CartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('cardNumber', TextType::class, ['label' => 'Card Number'])
            ->add('cardType', TextType::class, ['label' => 'Card Type'])
            ->add('expirationDate', DateType::class, ['widget' => 'single_text', 'label' => 'Expiration Date'])
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => fn(User $user) => $user->getFirstName() . ' ' . $user->getLastName(),
                'label' => 'Owner',
                'placeholder' => 'Select a user'
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Cart::class]);
    }
}
