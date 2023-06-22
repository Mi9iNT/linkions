<?php

namespace App\Service;

use Twig\Environment;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;

class MailerService
{
    private $mailer;
    private $twig;

    public function __construct(MailerInterface $mailer, Environment $twig)
    {
        $this->mailer = $mailer;
        $this->twig = $twig;
    }


    public function sendEmail($to, $from, $subject, $message, $username, $resetLink, $template)
    {
        $email = (new TemplatedEmail())
            ->from('linkions.contact@linkions.fr')
            ->to($to)
            ->replyTo($from)
            ->subject($subject)
            ->context([
                'de' => $from,
                'subject' => $subject,
                'message' => $message,
                'username' => $username,
                'resetLink' => $resetLink,
            ])
            ->htmlTemplate($template);

        // Envoyer l'e-mail en utilisant le service MailerInterface
        $this->mailer->send($email);
    }

    // Autres traitements après l'envoi de l'e-mail

    // ...
}
