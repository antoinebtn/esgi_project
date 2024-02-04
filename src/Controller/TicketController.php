<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Entity\Ticket;
use App\Form\CommentType;
use App\Form\EditTicketType;
use App\Form\TicketType;
use Doctrine\ORM\EntityManagerInterface;
use Psr\Container\ContainerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Workflow\Registry;

class TicketController extends AbstractController
{

    public function __construct(private Registry $registry){

    }

    #[Route('/ticket', name: 'app_ticket')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $tickets = array();
        $projects = $this->getUser()->getCompany()->getProjects();
        for ($i = 0; $i < count($projects); $i++) {
            $tickets = array_merge($tickets, $entityManager->getRepository(Ticket::class)->findBy(['project' => $projects[$i]])) ;
        }

        return $this->render('ticket/index.html.twig', [
            'tickets' => $tickets,
        ]);
    }

    #[Route('/ticket/my', name: 'app_my_ticket')]
    public function getMy(EntityManagerInterface $entityManager): Response
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
    public function show(int $id,EntityManagerInterface $entityManager, Request $request): Response
    {
        $ticket = $entityManager->getRepository(Ticket::class)->findOneBy(['id'=> $id]);
        $comments = $entityManager->getRepository(Comment::class)->findBy(['ticket' => $ticket]);

        // Nouveau commentaire
        $comment = new Comment();
        $form = $this->createForm(CommentType::class, $comment);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $comment = $form->getData();
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setUser($this->getUser());
            $comment->setTicket($ticket);

            $entityManager->persist($comment);
            $entityManager->flush();

            return $this->redirectToRoute('app_show_ticket', ['id' => $id]);
        }

        return $this->render('ticket/show.html.twig', [
            'ticket' => $ticket,
            'form' => $form,
            'comments' => $comments
        ]);
    }

    #[Route('/ticket/{id}/edit', name: 'app_edit_ticket', requirements: ['id' => '\d+'])]
    public function edit(int $id,EntityManagerInterface $entityManager, Request $request): Response
    {
        $ticket = $entityManager->getRepository(Ticket::class)->findOneBy(['id'=> $id]);

        $form = $this->createForm(EditTicketType::class, $ticket);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $ticket = $form->getData();

            $ticket->setUpdatedAt(new \DateTime());

            $entityManager->flush();

            return $this->redirectToRoute('app_show_ticket', ['id' => $id]);
        }

        return $this->render('ticket/edit.html.twig', [
            'ticket' => $ticket,
            'form' => $form
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

    #[Route('/ticket/transition', name: 'app_ticket_transition')]
    public function transitionTicket(EntityManagerInterface $entityManager, Request $request)
    {
        $data = json_decode($request->getContent(), true);

        $ticketId = $data['ticketId'];
        $transition = $data['transition'];

        $ticket = $entityManager->getRepository(Ticket::class)->findOneBy(['id'=> $ticketId]);
        $workflow = $this->registry->get($ticket, 'ticket_workflow');

        $workflow->apply($ticket, $transition);
        $entityManager->flush();

        return new JsonResponse(['success' => true]);
    }
}
