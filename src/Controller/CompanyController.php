<?php

namespace App\Controller;

use App\Entity\Company;
use App\Entity\Subscription;
use App\Entity\User;
use App\Form\RegistrationFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class CompanyController extends AbstractController
{
    #[Route('/company', name: 'app_company')]
    public function index(): Response
    {
        $company = $this->getUser()->getCompany();
        $subscription = $company->getSubscription();
        $users = $company->getUsers();
        $projects = $company->getProjects();
        $userMessage = '';
        $projectMessage = '';

        if (count($users) >= $subscription->getMaxUsers()){
            $userMessage = "Vous avez atteint le nombre maximum d'utilisateur autorisé";
        }
        if (count($projects) >= $subscription->getMaxProjects()){
            $projectMessage = "Vous avez atteint le nombre maximum de projet autorisé";
        }

        return $this->render('company/index.html.twig', [
            'company' => $company,
            'projects' => $projects,
            'users' => $users,
            'userMessage' => $userMessage,
            'projectMessage' => $projectMessage
        ]);
    }

    #[Route('/company/user/add', name: 'app_company_new_user')]
    public function addUser(Request $request, UserPasswordHasherInterface $userPasswordHasher,EntityManagerInterface $entityManager): Response
    {
        $company = $this->getUser()->getCompany();

        $user = new User();
        $form = $this->createForm(RegistrationFormType::class, $user, ['needCompany' => false]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $user->setPassword(
                $userPasswordHasher->hashPassword(
                    $user,
                    $form->get('plainPassword')->getData()
                )
            );

            $user->setCompany($company);

            $roles = $user->getRoles();
            $roles[] = 'ROLE_USER';
            $user->setRoles($roles);

            $entityManager->persist($user);
            $entityManager->flush();
            return $this->redirectToRoute('app_company');
        }

        return $this->render('company/newUser.html.twig', [
            'registrationForm' => $form->createView(),
        ]);

    }
}
