<?php

// src/Controller/PaymentController.php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\Checkout\Session;

class PaymentController extends AbstractController
{
  /**
   * @Route("/create-checkout-session", name="create_checkout_session")
   */
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
      'success_url' => $this->generateUrl('checkout_success', [], true),
      'cancel_url' => $this->generateUrl('checkout_cancel', [], true),
    ]);

    return $this->json(['id' => $checkout_session->id]);
  }

  /**
   * @Route("/checkout-success", name="checkout_success")
   */
  public function success(): Response
  {
    // Here you can handle what you want to do after a successful payment
    // For example, you might want to store this information in your database and then display a success message to the user

    return $this->render('payment/success.html.twig');
  }

  /**
   * @Route("/checkout-cancel", name="checkout_cancel")
   */
  public function cancel(): Response
  {
    // Here you can handle what you want to do after a canceled payment
    // For example, you might want to display a cancellation message to the user

    return $this->render('payment/cancel.html.twig');
  }
}
