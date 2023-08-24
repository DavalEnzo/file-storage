<?php

// src/Controller/PaymentController.php

namespace App\Controller;

use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Mapping\Entity;
use PharIo\Manifest\Url;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\Checkout\Session;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class PaymentController extends AbstractController
{
  private EntityManagerInterface $em;

  public function __construct(EntityManagerInterface $em)
  {
    $this->em = $em;
  }

 
   #Route("/create-checkout-session", name="create_checkout_session")
  
  public function create(): Response
  {
    \Stripe\Stripe::setApiKey("STRIPE_SECRET_KEY");

    $checkout_session = \Stripe\Checkout\Session::create([
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
      'success_url' => $this->generateUrl('checkout_success'),
      'cancel_url' => $this->generateUrl('checkout_cancel'),
    ]);

    return $this->json(['id' => $checkout_session->id]);
  }

  /**
   * @Route("/checkout-success", name="refus")
   */
  public function success(): Response
  {
    dd("success");
    return $this->render('payment/success.html.twig');
  }

  /**
   * @Route("/checkout-cancel", name="test")
   */
  public function cancel(): Response
  {
    // Here you can handle what you want to do after a canceled payment
    // For example, you might want to display a cancellation message to the user

    return $this->render('payment/cancel.html.twig');
  }
}
