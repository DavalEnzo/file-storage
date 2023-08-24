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

    /**
     * @throws ApiErrorException
     */
    #[Route('/payment/{user_id}', name: 'payment')]
    public function payment(int $user_id, EntityManagerInterface $entityManager): Response
    {
        $user = $entityManager->getRepository(User::class)->find($user_id);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        // Créez la session de paiement Stripe ici
        // STRIPE_PUBLIC_KEY=pk_test_51NSHcwAGkY1RmUpyD0tZ7hgkPdEEgpU96vTBmkbWCiXxltV3XAtI8lMFHGarH9bOTW5749aAoKdDbur3kZgeSR0m00pcsIg2ZG
        // STRIPE_SECRET_KEY=sk_test_51NSHcwAGkY1RmUpyFuQre4HkLRb0fMp0znI17RBwkaqYWXhQHLGR04YYjNmJmt6EDsfed47jKncXx05WiH7xRicY00aEMpWzWc
        // Initialise Stripe
        Stripe::setApiKey('sk_test_51NSHcwAGkY1RmUpyFuQre4HkLRb0fMp0znI17RBwkaqYWXhQHLGR04YYjNmJmt6EDsfed47jKncXx05WiH7xRicY00aEMpWzWc'); // Remplacez par votre clé secrète Stripe

        // Créez la session de paiement Stripe ici

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
    }

    #[Route('/success', name: 'checkout_success')]
    public function paymentSuccess(): Response
    {
        $user = $this->em->getRepository(User::class)->find($this->getUser()->getId());

        $user->setStatus(1);
        $storage = new Storage();
        $user->setStorage($storage);

        $this->em->persist($user);
        $this->em->flush();

        return $this->redirectToRoute('invoice');
    }

    #[Route('/payment/cancel', name: 'checkout_cancel')]
    public function paymentCancel(): Response
    {
        dd("cancel");
    }
}
