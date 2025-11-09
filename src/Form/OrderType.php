<?php

namespace App\Form;

use App\Entity\Order;
use App\Entity\User;
use App\Entity\Product;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;

class OrderType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('user', EntityType::class, [
                'class' => User::class,
                'choice_label' => fn($user) => $user->getFirstName() . ' ' . $user->getLastName()
            ])
            ->add('products', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'libelle',
                'multiple' => true,
                'expanded' => true,
            ])
            ->add('quantity', IntegerType::class);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults(['data_class' => Order::class]);
    }
}
