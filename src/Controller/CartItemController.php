<?php

namespace App\Controller;

use App\Entity\CartItem;
use App\Form\CartItemType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/cart/item')]
final class CartItemController extends AbstractController
{
    #[Route(name: 'app_cart_item_index', methods: ['GET'])]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $cartItems = $entityManager
            ->getRepository(CartItem::class)
            ->findAll();

        return $this->render('cart_item/index.html.twig', [
            'cart_items' => $cartItems,
        ]);
    }

    #[Route('/new', name: 'app_cart_item_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cartItem = new CartItem();
        $form = $this->createForm(CartItemType::class, $cartItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cartItem);
            $entityManager->flush();

            return $this->redirectToRoute('app_cart_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cart_item/new.html.twig', [
            'cart_item' => $cartItem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cart_item_show', methods: ['GET'])]
    public function show(CartItem $cartItem): Response
    {
        return $this->render('cart_item/show.html.twig', [
            'cart_item' => $cartItem,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_cart_item_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, CartItem $cartItem, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CartItemType::class, $cartItem);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cart_item_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('cart_item/edit.html.twig', [
            'cart_item' => $cartItem,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_cart_item_delete', methods: ['POST'])]
    public function delete(Request $request, CartItem $cartItem, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cartItem->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($cartItem);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cart_item_index', [], Response::HTTP_SEE_OTHER);
    }
}
