<?php

namespace App\Controller;

use Knp\Snappy\Pdf;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;


class InvoiceController extends AbstractController
{

    private $twig;
    private $pdf;

    public function __construct(Environment $twig, Pdf $pdf)
    {
        $this->twig = $twig;
        $this->pdf = $pdf;
    }

    /**
     * @Route("/invoice", name="invoice")
     */
    public function sendMail(MailerInterface $mailer)
    {
        $facture = $this->pdf->getOutputFromHtml($this->twig->render('mails/invoice.html.twig'));

        $numeroFacture = 1;

        $email = (new TemplatedEmail())
            ->from('davalenzo@zohomail.eu')
            ->to('davalenzo@gmail.com')
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Facture File Storage n° '. $numeroFacture)
            ->html('<img src="https://img.icons8.com/cotton/64/null/happy-file.png" alt="logo entreprise"><h1> Bonjour (nom + prenom), vous trouverez ci-joint votre facture concernant votre offre</h1>')
            ->attach($facture, 'facture.pdf', 'application/pdf');
        $mailer->send($email);
    }
}
