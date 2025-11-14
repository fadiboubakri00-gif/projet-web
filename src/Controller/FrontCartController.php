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
     * Show current basket (DB-backed, cart id stored in session under 'db_cart_id')
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
                    $qty = 1; // placeholder: adapt when adding quantity support
                    $subtotal = $p->getPrice() * $qty;
                    $items[] = ['product' => $p, 'qty' => $qty, 'subtotal' => $subtotal];
                    $total += $subtotal;
                }
            } else {
                // invalid cart id -> forget it
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
     * Add a product to cart.
     * If current user is not an App\Entity\User, save intended action and redirect to choose-auth.
     */
    #[Route('/basket/add/{id}', name: 'front_cart_add', methods: ['POST'])]
    public function add(int $id, Request $request): RedirectResponse
    {
        // CSRF check
        if (!$this->isCsrfTokenValid('basket_add'.$id, $request->request->get('_token'))) {
            $this->addFlash('danger', 'Invalid CSRF token.');
            return $this->redirectToRoute('app_shop_home');
        }

        // If not a front user, require login/register
        $user = $this->getUser();
        if (!($user instanceof \App\Entity\User)) {
            $request->getSession()->set('intended_add_id', $id);
            // optional: store return route name
            $request->getSession()->set('intended_return_route', 'front_cart_resume_add');
            $this->addFlash('info', 'Please login or register to add items to your cart.');
            return $this->redirectToRoute('user_choose_auth');
        }

        $product = $this->productRepo->find($id);
        if (!$product) {
            $this->addFlash('warning', 'Product not found.');
            return $this->redirectToRoute('app_shop_home');
        }

        // Create or get DB cart (session stored id)
        $cart = $this->getOrCreateCartForSession($request, $user);

        // Add product
        if (method_exists($cart, 'addProduct')) {
            $cart->addProduct($product);
        } else {
            $cart->getProducts()->add($product);
        }

        $this->em->flush();

        $this->addFlash('success', $product->getLibelle() . ' added to cart.');
        return $this->redirectToRoute('app_shop_home');
    }

    /**
     * Resume saved add-after-auth (call after login/register success redirect).
     * Reads 'intended_add_id' from session and performs add logic server-side.
     */
    #[Route('/basket/resume-add', name: 'front_cart_resume_add', methods: ['GET'])]
    public function resumeAdd(Request $request): RedirectResponse
    {
        $session = $request->getSession();
        $intended = $session->get('intended_add_id');

        if (!$intended) {
            return $this->redirectToRoute('app_shop_home');
        }

        // Ensure user is now a front User
        $user = $this->getUser();
        if (!($user instanceof \App\Entity\User)) {
            // still not an actual user — send back to auth page
            $this->addFlash('info', 'Please complete login or registration to continue.');
            return $this->redirectToRoute('user_choose_auth');
        }

        $product = $this->productRepo->find((int)$intended);
        if (!$product) {
            $session->remove('intended_add_id');
            $this->addFlash('warning', 'Product not found.');
            return $this->redirectToRoute('app_shop_home');
        }

        // Create or get cart, add product
        $cart = $this->getOrCreateCartForSession($request, $user);

        if (method_exists($cart, 'addProduct')) {
            $cart->addProduct($product);
        } else {
            $cart->getProducts()->add($product);
        }

        $this->em->flush();

        // cleanup intent and redirect to basket or shop as you prefer
        $session->remove('intended_add_id');
        $session->remove('intended_return_route');

        $this->addFlash('success', $product->getLibelle() . ' added to cart.');
        return $this->redirectToRoute('front_cart_index');
    }

    /**
     * Remove product from DB cart.
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
     * Checkout: create Order from Cart, remove cart, clear session.
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

        // create Order and copy products
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
        $this->em->remove($cart); // remove cart after conversion
        $this->em->flush();

        $session->remove('db_cart_id');

        $this->addFlash('success', 'Order created successfully. Order id: ' . $order->getId());
        return $this->redirectToRoute('app_shop_home');
    }

    /**
     * Helper: get or create a DB Cart, store id in session.
     * If a front User is provided, set as owner (only User, not Admin).
     */
    private function getOrCreateCartForSession(Request $request, ?\App\Entity\User $user = null): Cart
    {
        $session = $request->getSession();
        $cartId = $session->get('db_cart_id');

        $cart = $cartId ? $this->em->getRepository(Cart::class)->find($cartId) : null;

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

            $session->set('db_cart_id', $cart->getId());
        }

        return $cart;
    }
}
