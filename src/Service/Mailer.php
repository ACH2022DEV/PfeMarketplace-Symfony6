<?php
namespace App\Service;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;
use Symfony\Component\HttpFoundation\Response;

class Mailer
{

    public function __construct(Private readonly MailerInterface $mailer)
    {
    }

    public function sendEmail(
        $to = "wassimzouch34@gmail.com",
        $content = "<p>Sending emails is <em>fun</em> again!</p>",
        $subject = "Hello from Symfony Mailer5!"
    ): void
    {
        // Créer un email
        $email = (new Email())
            ->from('jane.doe@example.com')
            ->to($to)
            ->subject($subject)
            ->text('Sending emails is fun again!')
            ->html($content);

        // Envoyer l'email
        $this->mailer->send($email);

        // Répondre avec un message de confirmation
    }
}