<?php

namespace App\Controller;

use App\Repository\UserRepository;
use App\Repository\ProductRepository;
use App\Repository\OrderRepository;
use App\Repository\CartRepository;
use App\Repository\CategoryRepository; // Add this
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(
        UserRepository $userRepo,
        ProductRepository $productRepo,
        OrderRepository $orderRepo,
        CartRepository $cartRepo,
        CategoryRepository $categoryRepo // Add this
    ): Response
    {
        return $this->render('home/index.html.twig', [
            'userCount' => $userRepo->count([]),
            'productCount' => $productRepo->count([]),
            'categoryCount' => $categoryRepo->count([]),
            'cartCount' => $cartRepo->count([]),
            'orderCount' => $orderRepo->count([]),
        ]);
    }
}
