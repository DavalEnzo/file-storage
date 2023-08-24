<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationFormType;
use App\Security\UserAuthenticator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Authentication\UserAuthenticatorInterface;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class RegistrationController extends AbstractController
{
    #[Route('/inscription', name: 'app_register')]
    public function register(Request $request, UserPasswordHasherInterface $userPasswordHasher, UserAuthenticatorInterface $userAuthenticator, UserAuthenticator $authenticator, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            // encode the plain password
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setCreateDatetime(new \DateTimeImmutable());
            $user->setStatus(0);


            $entityManager->persist($user);
            $entityManager->flush();

            // Authenticate user
            $userAuthenticator->authenticateUser(
                $user,
                $authenticator,
                $request
            );

            // Redirect to payment
            return $this->redirectToRoute('payment', ['user_id' => $user->getId()]);
        }

        return $this->render('registration/register.html.twig', [
            'registrationForm' => $form->createView(),
            'title' => 'Inscription',
        ]);
    }

    #[Route('/payment/{user_id}', name: 'payment')]
    public function payment(int $user_id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($user_id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Créez la session de paiement Stripe ici

        // Initialise Stripe
        Stripe::setApiKey('STRIPE_SECRET_KEY'); // Remplacez par votre clé secrète Stripe

        // Créez la session de paiement Stripe ici
        try {
            $checkout_session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [[
                    'price_data' => [
                        'currency' => 'eur',
                        'product_data' => [
                            'name' => '20GB de stockage',
                        ],
                        'unit_amount' => 2000, // Montant à facturer en centimes de dollar
                    ],
                    'quantity' => 1,
                ]],
                'mode' => 'payment',
                'success_url' => $this->generateUrl('payment_success', [], UrlGeneratorInterface::ABSOLUTE_URL),
                'cancel_url' => $this->generateUrl('payment_cancel', [], UrlGeneratorInterface::ABSOLUTE_URL),
            ]);
        } catch (\Exception $e) {
            // Gérer les erreurs ici
        }

        return $this->render('payment/payment.html.twig', [
            'user' => $user,
            'checkout_session_id' => $checkout_session->id,
        ]);

        return $this->render('payment/payment.html.twig', [
            'user' => $user,
            // 'checkout_session' => $checkout_session,
        ]);
    }
    #[Route('/payment/success', name: 'payment_success')]
    public function paymentSuccess(Request $request, EntityManagerInterface $entityManager): Response
    {
        $sessionId = $request->query->get('session_id');

        if (!$sessionId) {
            // Redirigez vers une page d'erreur ou quelque chose du genre si l'ID de session n'est pas présent
        }

        // Récupérer l'utilisateur en fonction de l'ID de session, puis mettre à jour son statut
        // En fonction de votre mise en œuvre, vous devrez peut-être également communiquer avec l'API Stripe pour confirmer le paiement

        // Assurez-vous que l'utilisateur est bien récupéré et que le statut est mis à jour correctement

        // Par exemple, $user->setStatus(1);
        $entityManager->flush();

        return $this->render('payment/success.html.twig'); // Assurez-vous que cette vue existe
    }

    #[Route('/payment/cancel', name: 'payment_cancel')]
    public function paymentCancel(): Response
    {
        // Vous pouvez ajouter de la logique ici pour enregistrer l'annulation du paiement si nécessaire

        return $this->render('payment/cancel.html.twig'); // Assurez-vous que cette vue existe
    }
}
