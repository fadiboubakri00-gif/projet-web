<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use App\Repository\CartRepository;
use App\Repository\CategoryRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    // Dashboard route, requires login
    #[Route('/dashboard', name: 'app_home')]
    public function index(
        UserRepository $userRepo,
        ProductRepository $productRepo,
        OrderRepository $orderRepo,
        CartRepository $cartRepo,
        CategoryRepository $categoryRepo
    ): Response
    {
        // If user not logged in, redirect to login
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        return $this->render('home/index.html.twig', [
            'userCount' => $userRepo->count([]),
            'productCount' => $productRepo->count([]),
            'categoryCount' => $categoryRepo->count([]),
            'cartCount' => $cartRepo->count([]),
            'orderCount' => $orderRepo->count([]),
        ]);
    }
}
