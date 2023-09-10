<?php

namespace App\Controller;

use App\Entity\File;
use App\Entity\Invoice;
use App\Entity\Storage;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AdminController extends AbstractController
{
    protected EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
    )
    {
        $this->em = $em;
    }
    #[Route(path: '/admin', name: 'admin')]
    public function index(): Response
    {
        $allUsers = $this->em->getRepository(User::class)->findAll();

        $countUsers = count($allUsers);

        $storageUsed = $this->em->getRepository(Storage::class)->getTotalUsedStorage();

        $chiffreAffaire = $this->em->getRepository(Invoice::class)->getTotalNet();

        $fichiersAjd = $this->em->getRepository(File::class)->getTodayFiles();

        $totalFiles = $this->em->getRepository(File::class)->getTotalFiles();

        return $this->render('admin/admin.html.twig', [
            'title' => 'Administration',
            'users' => $allUsers,
            'countUsers' => $countUsers,
            'storageUsed' => $storageUsed,
            'chiffreAffaire' => $chiffreAffaire,
            'fichiersAjd' => $fichiersAjd,
            'totalFiles' => $totalFiles,
        ]);
    }
}
