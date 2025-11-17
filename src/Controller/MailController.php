<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailController extends AbstractController
{
    #[Route('/send-test', name: 'send_test_mail')]
    public function sendTest(MailerInterface $mailer): Response
    {
        $email = (new Email())
            ->from('no-reply@example.com')
            ->to('azizjboura27@gmail.com') // your real email
            ->subject('Hello from Symfony!')
            ->text('This is a test email sent from Symfony using Mailtrap.')
            ->html('<h2>It works perfectly 🎉</h2>');

        $mailer->send($email);

        return new Response('Test email sent!');
    }
}
