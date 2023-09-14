<?php

namespace App\Controller;

use App\Entity\Invoice;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ManagerRegistry;
use Knp\Snappy\Pdf;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\BinaryFileResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\ResponseHeaderBag;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Twig\Environment;


class InvoiceController extends AbstractController
{
    private EntityManagerInterface $em;
    private Environment $twig;
    private Pdf $pdf;

    public function __construct(
        EntityManagerInterface $em,
        Environment $twig,
        Pdf $pdf
    ) {
        $this->em = $em;
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

        file_put_contents($this->getParameter('upload_directory').'/facture-'.$newFacture->getInvoiceNumber().'-'.$newFacture->getCreateDate()->format("d-m-Y").'.pdf', $facture);

        return $this->redirectToRoute('index');
    }

    #[Route(path: '/invoice/list', name: 'invoice_list')]
    public function list(ManagerRegistry $managerRegistry): Response
    {
        $user = $this->getUser();

        $factures = $managerRegistry->getRepository(Invoice::class)->findBy(['user' => $user->getId()], ['invoice_number' => 'DESC']);

        return $this->render('invoice/invoice.html.twig', [
            'title' => 'Liste des factures',
            'factures' => $factures,
        ]);
    }


    #[Route(path: '/invoice/download/{id}', name: 'app_download_facture')]
    public function download(int $id):Response
    {
        $facture = $this->em->getRepository(Invoice::class)->find($id);

        if (!$facture) {
            $this->addFlash('warning', "fichier introuvable");
            return $this->redirectToRoute('invoice_list');
        }

        $fileName = "facture-".$facture->getInvoiceNumber()."-".$facture->getCreateDate()->format("d-m-Y").".pdf";
        $filePath = $this->getParameter('upload_directory') . "/" . $fileName;


        if (!file_exists($filePath)) {
            $this->addFlash('warning', "Le fichier demandé n'existe pas sur le serveur.");
            return $this->redirectToRoute('invoice_list');
        }

        $response = new BinaryFileResponse($filePath);
        $response->setContentDisposition(ResponseHeaderBag::DISPOSITION_ATTACHMENT, $fileName);

        $this->addFlash('success', 'Téléchargement du fichier initié avec succès !');
        return $response;
    }
}
