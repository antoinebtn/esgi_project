<?php

namespace App\Controller;

use App\Entity\Project;
use App\Form\ProjectType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ProjectController extends AbstractController
{
    #[Route('/project', name: 'app_project')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $companyId = $this->getUser()->getCompany()->getId();
        $projects = $entityManager->getRepository(Project::class)->findBy(['company' => $companyId]);

        return $this->render('project/index.html.twig', [
            'projects' => $projects,
        ]);
    }

    #[Route('/project/{id}', name: 'app_show_project', requirements: ['id' => '\d+'])]
    public function show(int $id, EntityManagerInterface $entityManager): Response
    {
        $companyId = $this->getUser()->getCompany()->getId();
        $project = $entityManager->getRepository(Project::class)->findOneBy(['id' => $id, 'company' => $companyId]);
        if ($project) {
            $tickets = $project->getTickets();
            return $this->render('project/show.html.twig', [
                'project' => $project,
                'tickets' => $tickets,
            ]);
        } else {
            return $this->redirectToRoute('app_home');
        }
    }

    #[Route('/project/add', name: 'app_project_add')]
    public function add(Request $request,EntityManagerInterface $entityManager): Response
    {
        $company = $this->getUser()->getCompany();

        $project = new Project();

        $form = $this->createForm(ProjectType::class,$project);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $project = $form->getData();
            $project->setCompany($company);

            $entityManager->persist($project);
            $entityManager->flush();

            return $this->redirectToRoute('app_project');
        }

        return $this->render('project/add.html.twig',[
            'form' => $form->createView(),
        ]);
    }
}
