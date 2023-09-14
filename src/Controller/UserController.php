<?php

namespace App\Controller;

use App\Entity\Storage;
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
    ) {
        $this->em = $em;
    }
    
    #[Route(path: '/delete/{id}', name: 'user_delete')]
    public function delete(int $id): Response
    {
        $user = $this->em->getRepository(User::class)->find($id);

        if (!$user) {
            $this->addFlash('warning', "utilisateur introuvable");
            return $this->redirectToRoute('admin');
        }

        $this->em->remove($user);

        $this->em->flush();

        $this->addFlash('success', "L'utilisateur a été supprimé !");
        return $this->redirectToRoute('admin');
    }

    #[Route(path: '/promotion/{id}', name: 'user_promotion')]
    public function promotion(int $id): Response
    {
        $user = $this->em->getRepository(User::class)->find($id);

        if (!$user) {
            $this->addFlash('warning', "utilisateur introuvable");
            return $this->redirectToRoute('admin');
        }

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

        if (!$user) {
            $this->addFlash('warning', "utilisateur introuvable");
            return $this->redirectToRoute('admin');
        }

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

        if (!$user) {
            $this->addFlash('warning', "utilisateur introuvable");
            return $this->redirectToRoute('admin');
        }

        $user->setStatus(1);
        $storage = $user->getStorage();

        if (!$storage) {
            $storage = new Storage();
            $user->setStorage($storage);
            $storage->setInitialCapacity(20000000000);  // 20 Go
            $storage->setLeftCapacity(20000000000);     // 20 Go
        } else {
            $storage->setInitialCapacity($storage->getInitialCapacity() + 20000000000);
            $storage->setLeftCapacity($storage->getLeftCapacity() + 20000000000);
        }

        $user->setPaymentsCount($user->getPaymentsCount() + 1);

        $this->em->persist($user);

        $this->em->flush();

        $this->addFlash('success', "L'utilisateur a été abonné !");
        return $this->redirectToRoute('admin');
    }

    #[Route(path: '/downgrade/{id}', name: 'user_downgrade')]
    public function downgrade(int $id): Response
    {
        $user = $this->em->getRepository(User::class)->find($id);

        if (!$user) {
            $this->addFlash('warning', "utilisateur introuvable");
            return $this->redirectToRoute('admin');
        }

        $user->setStatus(1);
        $storage = $user->getStorage();

        if (!$storage || $user->getPaymentsCount() === 0) {
            $this->addFlash('warning', "L'utilisateur n'a pas d'espace de stockage !");
        } else {
            $user->setPaymentsCount($user->getPaymentsCount() - 1);
            $storage->setInitialCapacity($storage->getInitialCapacity() - 20000000000);
            $storage->setLeftCapacity($storage->getLeftCapacity() - 20000000000);

            if ($user->getPaymentsCount() === 0) {
                $user->setStatus(0);
                $this->em->remove($user->getStorage());
            }
        }

        $this->em->persist($user);
        $this->em->flush();

        $this->addFlash('success', "L'utilisateur a perdu 20Go de stockage !");
        return $this->redirectToRoute('admin');
    }

    #[Route(path: '/desabonnement/{id}', name: 'user_desabonnement')]
    public function desabonnement(int $id): Response
    {
        $user = $this->em->getRepository(User::class)->find($id);

        if (!$user) {
            $this->addFlash('warning', "utilisateur introuvable");
            return $this->redirectToRoute('admin');
        }

        $user->setStatus(0);
        $user->setPaymentsCount(0);
        $this->em->remove($user->getStorage());

        $this->em->persist($user);

        $this->em->flush();

        $this->addFlash('success', "L'utilisateur a été désabonné !");
        return $this->redirectToRoute('admin');
    }
}
