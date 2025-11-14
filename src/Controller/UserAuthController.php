<?php
// src/Controller/UserAuthController.php
namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class UserAuthController extends AbstractController
{
    #[Route('/user/choose-auth', name: 'user_choose_auth')]
    public function chooseAuth(): Response
    {
        // a simple page with two big buttons: Login / Register
        // You will implement the actual login & register routes (below I assume /user/login and /user/register)
        return $this->render('user/choose_auth.html.twig', []);
    }
}
