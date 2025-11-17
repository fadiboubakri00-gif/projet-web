<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;

class EmailVerificationController extends AbstractController
{
    #[Route('/verify/email', name: 'app_verify_email')]
    public function verifyUserEmail(
        Request $request,
        UserRepository $userRepository,
        VerifyEmailHelperInterface $verifyEmailHelper,
        EntityManagerInterface $em
    ): Response {
        $id = $request->get('id');

        if (!$id) {
            $this->addFlash('error', 'Invalid verification link.');
            return $this->redirectToRoute('app_user_login');
        }

        $user = $userRepository->find($id);

        if (!$user) {
            $this->addFlash('error', 'User not found.');
            return $this->redirectToRoute('app_user_login');
        }

        try {
            $verifyEmailHelper->validateEmailConfirmation(
                $request->getUri(),
                $user->getId(),
                $user->getEmail()
            );

            $user->setIsVerified(true);
            $em->flush();

            $this->addFlash('success', 'Your email has been verified!');
        } catch (\Exception $e) {
            $this->addFlash('error', 'Invalid or expired verification link.');
        }

        return $this->redirectToRoute('app_user_login');
    }
}
