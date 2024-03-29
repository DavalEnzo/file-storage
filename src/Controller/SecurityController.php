<?php

namespace App\Controller;

use App\Repository\FilesRepository;
use App\Repository\UserRepository;
use App\Service\MailerSender;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Twig\Environment;

class SecurityController extends AbstractController
{
    protected EntityManagerInterface $em;

    public function __construct(
        EntityManagerInterface $em,
    )
    {
        $this->em = $em;
    }

    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
         if ($this->getUser()) {
             if($this->getUser()->getStatus() == 1){
                return $this->redirectToRoute('app_list');
             } else if ($this->getUser()->getStatus() == 0) {
                return $this->redirectToRoute('achat');
             }
         }

        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();
        // last username entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('security/login.html.twig', ['last_username' => $lastUsername, 'error' => $error, 'title' => 'Connexion']);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

    #[Route(path: '/delete', name: 'app_delete')]
    public function delete(
        Request $request,
        UserPasswordHasherInterface $passwordHasher,
        MailerSender $mailer,
        Environment $twig,
        UserRepository $userRepository,
        FilesRepository $filesRepository,
        TokenStorageInterface $tokenStorage,
    ): Response {
        $user = $this->getUser();

        if (!$user) {
            throw $this->createNotFoundException('Utilisateur introuvable.');
        }

        $form = $this->createFormBuilder()
            ->add('password', PasswordType::class)
            ->add('submit' , SubmitType::class, [
                'label' => 'Confirmer',
            ])
            ->getForm();

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->get('password')->getData();

            if (!$data) {
                $this->addFlash('warning', "Saisis incorrect.");
                return $this->redirectToRoute('app_delete');
            }

            if (!$passwordHasher->isPasswordValid($user, $data)) {
                $this->addFlash('warning', "Mot de passe incorrect.");
                return $this->redirectToRoute('app_delete');
            }

            $mailer->send(
                "davalenzo@zohomail.eu",
                $user->getEmail(),
                "Suppression du compte",
                $twig->render("mails/delete_account.html.twig", [
                    'user' => $user,
                ])
            );

            $admins = $userRepository->findUserByRole("ROLE_ADMIN");
            $countFiles = $filesRepository->getTotalFilesByUserId($user->getId());

            foreach ($admins as $admin) {
                $mailer->send(
                    "davalenzo@zohomail.eu",
                    $admin->getEmail(),
                    "Suppression du compte",
                    $twig->render("mails/delete_account_admin.html.twig", [
                        'admin' => $admin,
                        'user' => $user,
                        'countFiles' => $countFiles ?? 0,
                    ])
                );
            }

            $this->em->remove($user);
            $this->em->flush();

            $this->addFlash('success', "Vous avez supprimé votre compte avec succés");

            $tokenStorage->setToken(null);

            return $this->redirectToRoute('app_logout');
        }

        return $this->render('list/validateDeletion.html.twig', [
            'form' => $form,
        ]);
    }

    #[Route(path: '/redirect', name: 'redirection')]
    public function redirection(): RedirectResponse
    {
        $this->addFlash('success', 'Vous avez été déconnecté !');
        return $this->redirectToRoute('index');
    }
}
