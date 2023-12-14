<?php

namespace App\Controller;

use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Stripe\StripeClient;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CheckoutController extends AbstractController
{
    #[Route('/checkout', name: 'app_checkout')]
    public function index(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {

        $id = $session->get('subscription_id');
        $subscription = $entityManager->getRepository(Subscription::class)->findOneBy(['id' => $id]);

        $stripe = new StripeClient($_ENV['STRIPE_SECRET_KEY']);

        $clientSecret = $stripe->checkout->sessions->create([
            'mode' => 'subscription',
            'line_items' => [
                [
                    'price' => $subscription->getStripeSubscriptionId(),
                    'quantity' => 1,
                ],
            ],
            'ui_mode' => 'embedded',
            'return_url' => 'https://127.0.0.1:8000/checkout/return?session_id={CHECKOUT_SESSION_ID}',
        ]);

        $session->set('client_secret', $clientSecret);

        return $this->render('checkout/index.html.twig',[
            "subscription" => $subscription,
            "clientSecret" => $clientSecret
        ]);
    }

    #[Route('/checkout/return', name: 'app_checkout_success')]
    public function success(SessionInterface $session, EntityManagerInterface $entityManager): Response
    {
        $clientSecret = $session->get('client_secret');
        $id = $session->get('subscription_id');
        $subscription = $entityManager->getRepository(Subscription::class)->findOneBy(['id' => $id]);
        $user = $this->getUser();

        $company = $user->getCompany();

        $company->setSubscription($subscription);
        $entityManager->flush();

        return $this->render('checkout/success.html.twig',[
            "subscription" => $subscription,
            "clientSecret" => $clientSecret
        ]);
    }
}
