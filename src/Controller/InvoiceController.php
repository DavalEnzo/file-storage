<?php

namespace App\Controller;

use App\Entity\Invoice;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Snappy\Pdf;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;


class InvoiceController extends AbstractController
{

    private Environment $twig;
    private Pdf $pdf;

    public function __construct(Environment $twig, Pdf $pdf)
    {
        $this->twig = $twig;
        $this->pdf = $pdf;
    }

    #[Route(path: '/invoice', name: 'invoice')]
    public function sendMail(MailerInterface $mailer, ManagerRegistry $managerRegistry): Response
    {
        if (!$this->getUser()) {
            return $this->redirectToRoute('app_login');
        }

        $user = $this->getUser();

        $facture = new Invoice();

        $oldUserFacture = $managerRegistry->getRepository(Invoice::class)->findOneBy(['user' => $user->getId()], ['invoice_number' => 'DESC']);

        if($oldUserFacture)
        {
            $facture->setInvoiceNumber($oldUserFacture->getInvoiceNumber() + 1);
        } else {
            $facture->setInvoiceNumber(1);
        }

        $facture->setCreateDate(new \DateTime());
        $facture->setUser($user);

        $entityManager = $managerRegistry->getManager();

        $entityManager->persist($facture);
        $entityManager->flush();

        $newFacture = $entityManager->getRepository(Invoice::class)->findOneBy(['user' => $user->getId()], ['invoice_number' => 'DESC']);

        $facture = $this->pdf->getOutputFromHtml($this->twig->render('mails/invoice.html.twig',
            [
                'facture' => $newFacture
            ]
        ));

        $email = (new TemplatedEmail())
            ->from('davalenzo@zohomail.eu')
            ->to($user->getEmail())
            //->cc('cc@example.com')
            //->bcc('bcc@example.com')
            //->replyTo('fabien@example.com')
            //->priority(Email::PRIORITY_HIGH)
            ->subject('Facture File Storage n° ' . $newFacture->getInvoiceNumber())
            ->html('<img src="https://img.icons8.com/cotton/64/null/happy-file.png" alt="logo entreprise"><p> Bonjour M/Mme ' . $user->getLastName() . ', vous trouverez ci-joint votre facture concernant votre offre</p>')
            ->attach($facture, 'facture.pdf', 'application/pdf');
        $mailer->send($email);

        return $this->redirectToRoute('index');
    }
}
