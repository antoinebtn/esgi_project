<?php

namespace App\Controller;

use App\Entity\Ticket;
use App\Form\TicketType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class TicketController extends AbstractController
{
    #[Route('/ticket', name: 'app_ticket')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tickets = $entityManager->getRepository(Ticket::class)->findAll();

        return $this->render('ticket/index.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    #[Route('/ticket/my', name: 'app_my_ticket')]
    public function getOne(EntityManagerInterface $entityManager): Response
    {
        if($this->getUser()){
            $tickets = $entityManager->getRepository(Ticket::class)->findBy(['assignment'=> $this->getUser()]);
            return $this->render('ticket/index.html.twig', [
                'tickets' => $tickets,
            ]);
        } else {
            return  $this->redirectToRoute('app_login');
        }
    }

    #[Route('/ticket/{id}', name: 'app_show_ticket', requirements: ['id' => '\d+'])]
    public function show(int $id,EntityManagerInterface $entityManager): Response
    {
        $ticket = $entityManager->getRepository(Ticket::class)->findOneBy(['id'=> $id]);

        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
        ]);
    }
    #[Route('/ticket/add', name: 'app_ticket_add')]
    public function add(Request $request,EntityManagerInterface $entityManager): Response
    {
        $ticket = new Ticket();

        $form = $this->createForm(TicketType::class,$ticket);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ticket = $form->getData();
            $ticket->setCreatedAt(new \DateTimeImmutable());
            $ticket->setUpdatedAt(new \DateTime());
            $ticket->setAssignment($this->getUser());

            $entityManager->persist($ticket);
            $entityManager->flush();

            return $this->redirectToRoute('app_home');
        }

        return $this->render('ticket/add.html.twig',[
            'form' => $form->createView(),
        ]);
    }
}
