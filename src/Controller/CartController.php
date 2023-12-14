<?php

namespace App\Controller;

use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    #[Route('/cart/{id}', name: 'app_cart')]
    public function index($id,EntityManagerInterface $entityManager,SessionInterface $session): Response
    {
        $subscription = $entityManager->getRepository(Subscription::class)->findOneBy(['id' => $id]);
        $session->set('subscription_id', $id);
        if ($this->getUser() && $subscription->getName() != 'Free'){
            return $this->render('cart/index.html.twig',[
                'subscription' => $subscription
            ]);
        } else {
            return $this->redirectToRoute('app_register');
        }
    }
}
