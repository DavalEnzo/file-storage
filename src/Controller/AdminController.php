<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
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
    public function index(Request $request): Response
    {
        $allUsers = $this->em->getRepository(User::class)->findAll();

        return $this->render('admin/admin.html.twig', [
            'title' => 'Administration',
            'users' => $allUsers,
        ]);
    }
}
