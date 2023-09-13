<?php

namespace App\Service;

use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\Mailer\MailerInterface;
use Twig\Environment;

class MailerSender
{
    protected MailerInterface $mailer;

    public function __construct(MailerInterface $mailer)
    {
        $this->mailer = $mailer;
    }

    public function send(string $from, string $email, string $subject, string $template, array $attachments = []): bool
    {
        $email = (new TemplatedEmail())
            ->from($from)
            ->to($email)
            ->subject($subject)
            ->html($template);

        if (empty($attachments)) {
            $this->mailer->send($email);

            return true;
        }

        foreach ($attachments as $attachment) {
            if (!array_key_exists('file', $attachment) ||
                !array_key_exists('name', $attachment) ||
                !array_key_exists('type', $attachment)) {
                return false;
            }

            $email->attach($attachment["file"], $attachment["name"], $attachment["type"]);
        }

        $this->mailer->send($email);

        return true;
    }
}
