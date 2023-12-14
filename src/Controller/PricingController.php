<?php

namespace App\Controller;

use App\Entity\Subscription;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PricingController extends AbstractController
{
    #[Route('/pricing', name: 'app_pricing')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $subscriptions = $entityManager->getRepository(Subscription::class)->findAll();
        return $this->render('pricing/index.html.twig',[
            "subscriptions" => $subscriptions
        ]);
    }
}
