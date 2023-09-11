<?php

namespace App\Controller;

use App\Entity\Storage;
use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\Checkout\Session;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Stripe\Stripe;
use Symfony\Component\HttpFoundation\RedirectResponse;

class RegistrationController extends AbstractController
{
    private EntityManagerInterface $em;

    public function __construct(EntityManagerInterface $em)
    {
        $this->em = $em;
    }

    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setCreateDatetime(new \DateTimeImmutable());
            $user->setStatus(0);

            $this->em->persist($user);
            $this->em->flush();

            $this->addFlash('success', 'Inscription réussie ! Vous avez été redirigé vers la page de paiement.');

            // Authenticate user
            $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );

            return $this->redirectToRoute('payment', ['user_id' => $user->getId()]);
        } elseif ($form->isSubmitted()) {
            $this->addFlash('error', 'Erreur lors de l\'inscription. Vérifiez vos données et essayez à nouveau.');
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'title' => 'Inscription',
        ]);
    }

    #[Route('/payment/{user_id}', name: 'payment')]
    public function payment(int $user_id): Response
    {
        $user = $this->em->getRepository(User::class)->find($user_id);

        if (!$user || $user_id != $this->getUser()->getId()) {
            $this->addFlash('error', 'Erreur de route.');
            return $this->redirectToRoute('index');
        }

        $stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'];
        Stripe::setApiKey($stripeSecretKey);

        try {
            $checkout_session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => '20Go de stockage',
                        ],
                        'unit_amount' => 2000,
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $this->generateUrl('checkout_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('checkout_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);

            return new RedirectResponse($checkout_session->url, 303);
        } catch (ApiErrorException $e) {
            $this->addFlash('error', 'Erreur lors de la création de la session de paiement.');
            return $this->redirectToRoute('app_register');
        }
    }

    #[Route('/success', name: 'checkout_success')]
    public function paymentSuccess(): Response
    {
        $user = $this->em->getRepository(User::class)->find($this->getUser()->getId());

        if (!$user) {
            $this->addFlash('error', 'Erreur de serveur. Utilisateur non trouvé.');
            return $this->redirectToRoute('index');
        }

        $user->setStatus(1);

        $storage = $user->getStorage();

        if (!$storage) {
            $storage = new Storage();
            $user->setStorage($storage);
            $storage->setInitialCapacity(20000000000);  // 20 Go
            $storage->setLeftCapacity(20000000000);     // 20 Go
        } else {
            $user->setPaymentsCount($user->getPaymentsCount() + 1);
            $storage->setInitialCapacity($storage->getInitialCapacity() + 20000000000);
            $storage->setLeftCapacity($storage->getLeftCapacity() + 20000000000);
        }

        $this->em->persist($user);
        $this->em->flush();

        $this->addFlash('success', 'Votre capacité de stockage a été augmentée de 20 Go.');
        return $this->redirectToRoute('invoice');
    }

    #[Route('/cancel', name: 'checkout_cancel')]
    public function paymentCancel(): Response
    {
        $this->addFlash('error', 'Le paiement a été annulé.');
        return $this->redirectToRoute('index');
    }

    #[Route('/check-storage', name: 'check_storage')]
    public function checkStorageLimit(): Response
    {
        $user = $this->getUser();
        if (!$user) {
            $this->addFlash('error', 'Erreur de serveur. Utilisateur non trouvé.');
            return $this->redirectToRoute('index');
        }

        $storage = $user->getStorage();

        if (!$storage) {
            $this->addFlash('error', 'Erreur inattendue. Stockage non trouvé pour l\'utilisateur.');
            return $this->redirectToRoute('index');
        }

        if ($storage->getLeftCapacity() <= 0) {
            $this->addFlash('warning', 'Votre capacité de stockage est épuisée. Veuillez acheter de l\'espace supplémentaire.');
            return $this->redirectToRoute('payment', ['user_id' => $user->getId()]);
        }

        $this->addFlash('success', 'Tout va bien avec votre espace de stockage.');
        return $this->redirectToRoute('index');
    }
}
