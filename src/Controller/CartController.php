<?php

namespace App\Controller;

use App\Entity\Cart;
use App\Form\CartType;
use App\Repository\CartRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cart')]
final class CartController extends AbstractController
{
    #[Route(name: 'app_cart_index', methods: ['GET'])]
    public function index(CartRepository $cartRepository): Response
    {
        return $this->render('cart/index.html.twig', [
            'carts' => $cartRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_cart_new', methods: ['GET', 'POST'])]
public function new(Request $request, EntityManagerInterface $entityManager, CartRepository $cartRepository): Response
{
    $cart = new Cart();
    $form = $this->createForm(CartType::class, $cart);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $user = $cart->getOwner();
        
        // Double check if user already has a cart (in case form was manipulated)
        $existingCart = $cartRepository->findOneBy(['owner' => $user]);
        
        if ($existingCart) {
            $this->addFlash('error', 'This user already has a cart. Each user can only have one cart.');
            return $this->render('cart/new.html.twig', [
                'cart' => $cart,
                'form' => $form,
            ]);
        }

        try {
            $entityManager->persist($cart);
            $entityManager->flush();
            
            $this->addFlash('success', 'Cart created successfully.');
            return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
            
        } catch (\Exception $e) {
            $this->addFlash('error', 'This user already has a cart. Each user can only have one cart.');
            return $this->render('cart/new.html.twig', [
                'cart' => $cart,
                'form' => $form,
            ]);
        }
    }

    return $this->render('cart/new.html.twig', [
        'cart' => $cart,
        'form' => $form,
    ]);
}
    #[Route('/{id}', name: 'app_cart_show', methods: ['GET'])]
    public function show(Cart $cart): Response
    {
        return $this->render('cart/show.html.twig', [
            'cart' => $cart,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cart_edit', methods: ['GET', 'POST'])]
public function edit(Request $request, Cart $cart, EntityManagerInterface $entityManager, CartRepository $cartRepository): Response
{
    $form = $this->createForm(CartType::class, $cart);
    $form->handleRequest($request);

    if ($form->isSubmitted() && $form->isValid()) {
        $user = $cart->getOwner();
        
        // Check if changing to a user who already has a different cart
        $existingCart = $cartRepository->findOneBy(['owner' => $user]);
        
        if ($existingCart && $existingCart->getId() !== $cart->getId()) {
            $this->addFlash('error', 'This user already has a cart. Each user can only have one cart.');
            return $this->render('cart/edit.html.twig', [
                'cart' => $cart,
                'form' => $form,
            ]);
        }

        try {
            $entityManager->flush();
            $this->addFlash('success', 'Cart updated successfully.');
            return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
        } catch (\Exception $e) {
            $this->addFlash('error', 'Error updating cart. Please try again.');
            return $this->render('cart/edit.html.twig', [
                'cart' => $cart,
                'form' => $form,
            ]);
        }
    }

    return $this->render('cart/edit.html.twig', [
        'cart' => $cart,
        'form' => $form,
    ]);
}

    #[Route('/{id}', name: 'app_cart_delete', methods: ['POST'])]
    public function delete(Request $request, Cart $cart, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cart->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cart);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cart_index', [], Response::HTTP_SEE_OTHER);
    }
}
