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
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class HomeController extends AbstractController
{
    // Root route → redirects based on login status
    #[Route('/', name: 'app_root')]
    public function rootRedirect(AuthorizationCheckerInterface $authChecker): Response
    {
        // If user is already logged in (ROLE_ADMIN or any authenticated user)
        if ($authChecker->isGranted('IS_AUTHENTICATED_FULLY')) {
            return $this->redirectToRoute('app_home'); // go to dashboard
        }

        // Otherwise, go to login page
        return $this->redirectToRoute('app_login');
    }

    // Dashboard route (requires login)
    #[Route('/dashboard', name: 'app_home')]
    public function index(
        UserRepository $userRepo,
        ProductRepository $productRepo,
        OrderRepository $orderRepo,
        CartRepository $cartRepo,
        CategoryRepository $categoryRepo
    ): Response {
        // Ensure only admins can access dashboard
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
