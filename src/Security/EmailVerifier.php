<?php

namespace App\Security;

use SymfonyCasts\Bundle\VerifyEmail\VerifyEmailHelperInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use App\Entity\User;

class EmailVerifier
{
    public function __construct(
        private VerifyEmailHelperInterface $verifyEmailHelper,
        private MailerInterface $mailer
    ) {}

    public function sendEmailConfirmation(string $routeName, User $user): void
    {
        $signature = $this->verifyEmailHelper->generateSignature(
            $routeName,
            $user->getId(),
            $user->getEmail()
        );

        $email = (new Email())
            ->from('no-reply@example.com')
            ->to($user->getEmail())
            ->subject('Verify Your Email')
            ->html("<p>Click to verify your email:</p>
                    <p><a href='" . $signature->getSignedUrl() . "'>Verify Email</a></p>");

        $this->mailer->send($email);
    }

    public function validateEmail(int $id, string $email): void
    {
        $this->verifyEmailHelper->validateEmailConfirmation($id, $email);
    }
}
