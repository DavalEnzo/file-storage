<?php

namespace App\Controller;

use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route(path: '/user')]
class UserController extends AbstractController
{
    protected EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
    )
    {
        $this->em = $em;
    }
    #[Route(path: '/delete/{id}', name: 'user_delete')]
    public function delete(int $id): Response
    {
        $user = $this->em->getRepository(User::class)->find($id);
        $this->em->remove($user);

        $this->em->flush();

        $this->addFlash('success', "L'utilisateur a supprimé !");
        return $this->redirectToRoute('admin');
    }

    #[Route(path: '/promotion/{id}', name: 'user_promotion')]
    public function promotion(int $id): Response
    {
        $user = $this->em->getRepository(User::class)->find($id);
        $user->setRoles(['ROLE_ADMIN']);
        $this->em->persist($user);

        $this->em->flush();

        $this->addFlash('success', "L'utilisateur a été promu !");
        return $this->redirectToRoute('admin');
    }

    #[Route(path: '/demote/{id}', name: 'user_demote')]
    public function demote(int $id): Response
    {
        $user = $this->em->getRepository(User::class)->find($id);
        $user->setRoles(['ROLE_USER']);
        $this->em->persist($user);

        $this->em->flush();

        $this->addFlash('success', "L'utilisateur a été rétrogradé !");
        return $this->redirectToRoute('admin');
    }

    #[Route(path: '/abonnement/{id}', name: 'user_abonnement')]
    public function abonnement(int $id): Response
    {
        $user = $this->em->getRepository(User::class)->find($id);
        $user->setStatus(1);
        $this->em->persist($user);

        $this->em->flush();

        $this->addFlash('success', "L'utilisateur a été abonné !");
        return $this->redirectToRoute('admin');
    }

    #[Route(path: '/desabonnement/{id}', name: 'user_desabonnement')]
    public function desabonnement(int $id): Response
    {
        $user = $this->em->getRepository(User::class)->find($id);
        $user->setStatus(0);
        $this->em->persist($user);

        $this->em->flush();

        $this->addFlash('success', "L'utilisateur a été désabonné !");
        return $this->redirectToRoute('admin');
    }
}
