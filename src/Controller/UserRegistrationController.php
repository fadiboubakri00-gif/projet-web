<?php
// src/Controller/UserRegistrationController.php
namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class UserRegistrationController extends AbstractController
{
    #[Route('/user/register', name: 'app_user_register', methods: ['GET','POST'])]
    public function register(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        if ($request->isMethod('POST')) {
            $email = (string)$request->request->get('email');
            $username = (string)$request->request->get('username');
            $plain = (string)$request->request->get('password');

            if (!$email || !$plain || !$username) {
                $this->addFlash('danger', 'Please fill all fields.');
                return $this->redirectToRoute('app_user_register');
            }

            // check existing
            $existing = $em->getRepository(User::class)->findOneBy(['email' => $email]);
            if ($existing) {
                $this->addFlash('warning', 'Email is already registered. Please login.');
                return $this->redirectToRoute('app_user_login');
            }

            $user = new User();
            $user->setEmail($email);
            $user->setUsername($username);

            $hashed = $passwordHasher->hashPassword($user, $plain);
            $user->setPassword($hashed);

            $em->persist($user);
            $em->flush();

            $this->addFlash('success', 'Registration successful. Please login.');

            // optional: redirect to login so user can sign in and resume cart
            return $this->redirectToRoute('app_user_login');
        }

        return $this->render('user/register.html.twig');
    }
}
