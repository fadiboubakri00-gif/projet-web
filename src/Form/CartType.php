<?php

namespace App\Form;

use App\Entity\Cart;
use App\Entity\Product;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class CartType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $currentCart = $options['data'] ?? null;
        $currentOwnerId = $currentCart ? $currentCart->getOwner()?->getId() : null;

        $builder
            ->add('owner', EntityType::class, [
                'class' => User::class,
                'choice_label' => 'username',
                'query_builder' => function (UserRepository $userRepository) use ($currentOwnerId) {
                    // Show users without carts OR the current owner of this cart
                    return $userRepository->createQueryBuilder('u')
                        ->leftJoin('u.cart', 'c')
                        ->where('c.id IS NULL OR u.id = :currentOwnerId')
                        ->setParameter('currentOwnerId', $currentOwnerId);
                },
            ])
            ->add('products', EntityType::class, [
                'class' => Product::class,
                'choice_label' => 'libelle',
                'multiple' => true,
                'expanded' => true,
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Cart::class,
        ]);
    }
}