<?php

namespace App\Controller;

use App\Entity\Company;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{
    #[Route('/company', name: 'app_company')]
    public function index(EntityManagerInterface $entityManager): Response
    {
        $companyId = $this->getUser()->getCompany()->getId();

        $company = $entityManager->getRepository(Company::class)->findOneBy(['id'=> $companyId]);

        $users = $company->getUsers();

        $projects = $company->getProjects();

        return $this->render('company/index.html.twig', [
            'company' => $company,
            'projects' => $projects,
            'users' => $users,
        ]);
    }
}
