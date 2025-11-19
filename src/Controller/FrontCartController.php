<?php
// src/Controller/FrontCartController.php
namespace App\Controller;

use App\Entity\Cart;
use App\Entity\Order;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

class FrontCartController extends AbstractController
{
    private EntityManagerInterface $em;
    private ProductRepository $productRepo;

    public function __construct(EntityManagerInterface $em, ProductRepository $productRepo)
    {
        $this->em = $em;
        $this->productRepo = $productRepo;
    }

    /**
     * Show basket
     */
    #[Route('/basket', name: 'front_cart_index', methods: ['GET'])]
    public function index(Request $request): Response
    {
        $session = $request->getSession();
        $cartId = $session->get('db_cart_id');

        $items = [];
        $total = 0;

        if ($cartId) {
            $cart = $this->em->getRepository(Cart::class)->find($cartId);
            if ($cart) {
                foreach ($cart->getProducts() as $p) {
                    $qty = 1; // placeholder for quantity
                    $subtotal = $p->getPrice() * $qty;
                    $items[] = ['product' => $p, 'qty' => $qty, 'subtotal' => $subtotal];
                    $total += $subtotal;
                }
            } else {
                $session->remove('db_cart_id');
            }
        }

        return $this->render('shop/basket_db.html.twig', [
            'items' => $items,
            'total' => $total,
            'cartId' => $cartId,
        ]);
    }

    /**
     * Add product to basket (AJAX)
     */
    #[Route('/basket/add/{id}', name: 'front_cart_add', methods: ['POST'])]
    public function add(int $id, Request $request): JsonResponse
    {
        if (!$this->isCsrfTokenValid('basket_add'.$id, $request->request->get('_token'))) {
            return new JsonResponse(['success' => false, 'message' => 'Invalid CSRF token.'], 400);
        }

        $user = $this->getUser();

        // Redirect non-shop users to login
        if (!($user instanceof \App\Entity\User)) {
            $request->getSession()->set('intended_add_id', $id);
            $request->getSession()->set('intended_return_route', 'front_cart_resume_add');
            return new JsonResponse(['success' => false, 'redirect' => $this->generateUrl('app_user_login'), 'message' => 'Please login to add items.'], 401);
        }

        $product = $this->productRepo->find($id);
        if (!$product) {
            return new JsonResponse(['success' => false, 'message' => 'Product not found.'], 404);
        }

        $cart = $this->getOrCreateCartForSession($request, $user);

        if (method_exists($cart, 'addProduct')) {
            $cart->addProduct($product);
        } else {
            $cart->getProducts()->add($product);
        }

        $this->em->flush();

        $cartCount = count($cart->getProducts());

        return new JsonResponse([
            'success' => true,
            'message' => $product->getLibelle() . ' added to cart.',
            'cartCount' => $cartCount
        ]);
    }

    /**
     * Resume add after login
     */
    #[Route('/basket/resume-add', name: 'front_cart_resume_add', methods: ['GET'])]
    public function resumeAdd(Request $request): RedirectResponse
    {
        $session = $request->getSession();
        $intended = $session->get('intended_add_id');

        if (!$intended) {
            return $this->redirectToRoute('app_shop_home');
        }

        $user = $this->getUser();
        if (!($user instanceof \App\Entity\User)) {
            $this->addFlash('info', 'Please login or register to continue.');
            return $this->redirectToRoute('app_user_login');
        }

        $product = $this->productRepo->find((int)$intended);
        if (!$product) {
            $session->remove('intended_add_id');
            $this->addFlash('warning', 'Product not found.');
            return $this->redirectToRoute('app_shop_home');
        }

        $cart = $this->getOrCreateCartForSession($request, $user);

        if (method_exists($cart, 'addProduct')) {
            $cart->addProduct($product);
        } else {
            $cart->getProducts()->add($product);
        }

        $this->em->flush();
        $session->remove('intended_add_id');
        $session->remove('intended_return_route');

        $this->addFlash('success', $product->getLibelle() . ' added to cart.');
        return $this->redirectToRoute('front_cart_index');
    }

    /**
     * Get cart count (for navbar badge)
     */
    #[Route('/basket/count', name: 'front_cart_count', methods: ['GET'])]
    public function getCartCount(Request $request): JsonResponse
    {
        $session = $request->getSession();
        $cartId = $session->get('db_cart_id');
        $count = 0;

        if ($cartId) {
            $cart = $this->em->getRepository(Cart::class)->find($cartId);
            if ($cart) {
                $count = count($cart->getProducts());
            }
        }

        return new JsonResponse(['count' => $count]);
    }

    /**
     * Remove product from basket
     */
    #[Route('/basket/remove/{id}', name: 'front_cart_remove', methods: ['POST'])]
    public function remove(int $id, Request $request): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('basket_remove'.$id, $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');
            return $this->redirectToRoute('front_cart_index');
        }

        $session = $request->getSession();
        $cartId = $session->get('db_cart_id');

        if (!$cartId) {
            $this->addFlash('warning', 'No active cart.');
            return $this->redirectToRoute('front_cart_index');
        }

        $cart = $this->em->getRepository(Cart::class)->find($cartId);
        if (!$cart) {
            $session->remove('db_cart_id');
            $this->addFlash('warning', 'Cart not found.');
            return $this->redirectToRoute('front_cart_index');
        }

        $product = $this->productRepo->find($id);
        if ($product) {
            if (method_exists($cart, 'removeProduct')) {
                $cart->removeProduct($product);
            } else {
                $cart->getProducts()->removeElement($product);
            }
            $this->em->flush();
            $this->addFlash('success', 'Item removed from cart.');
        }

        return $this->redirectToRoute('front_cart_index');
    }

    /**
     * Checkout
     */
    #[Route('/basket/checkout', name: 'front_cart_checkout', methods: ['POST'])]
    public function checkout(Request $request): RedirectResponse
    {
        if (!$this->isCsrfTokenValid('basket_checkout', $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');
            return $this->redirectToRoute('front_cart_index');
        }

        $session = $request->getSession();
        $cartId = $session->get('db_cart_id');
        if (!$cartId) {
            $this->addFlash('warning', 'No active cart to checkout.');
            return $this->redirectToRoute('front_cart_index');
        }

        $cart = $this->em->getRepository(Cart::class)->find($cartId);
        if (!$cart) {
            $session->remove('db_cart_id');
            $this->addFlash('warning', 'Cart not found.');
            return $this->redirectToRoute('app_shop_home');
        }

        $order = new Order();
        if (method_exists($order, 'setCreatedAt')) {
            $order->setCreatedAt(new \DateTimeImmutable());
        }

        $user = $this->getUser();
        if ($user instanceof \App\Entity\User && method_exists($order, 'setOrderUser')) {
            $order->setOrderUser($user);
        }

        $total = 0;
        foreach ($cart->getProducts() as $p) {
            if (method_exists($order, 'addProduct')) {
                $order->addProduct($p);
            } else {
                $order->getProducts()->add($p);
            }
            $total += $p->getPrice();
        }

        if (method_exists($order, 'setTotalPrice')) {
            $order->setTotalPrice($total);
        }

        $this->em->persist($order);
        $this->em->remove($cart);
        $this->em->flush();

        $session->remove('db_cart_id');

        $this->addFlash('success', 'Order created successfully. Order id: ' . $order->getId());
        return $this->redirectToRoute('app_shop_home');
    }

    /**
     * Helper: create or retrieve cart
     */
    private function getOrCreateCartForSession(Request $request, ?\App\Entity\User $user = null): Cart
    {
        $session = $request->getSession();
        $cartId = $session->get('db_cart_id');

        // Try to get cart from session
        $cart = $cartId ? $this->em->getRepository(Cart::class)->find($cartId) : null;

        // If no cart in session, try to get existing cart for user
        if (!$cart && $user instanceof \App\Entity\User) {
            $cart = $this->em->getRepository(Cart::class)->findOneBy(['owner' => $user]);
        }

        // If no cart, create new one
        if (!$cart) {
            $cart = new Cart();
            if ($user instanceof \App\Entity\User && method_exists($cart, 'setOwner')) {
                $cart->setOwner($user);
            }
            if (method_exists($cart, 'setCreatedAt')) {
                $cart->setCreatedAt(new \DateTimeImmutable());
            }

            $this->em->persist($cart);
            $this->em->flush();
        }

        $session->set('db_cart_id', $cart->getId());

        return $cart;
    }
}
