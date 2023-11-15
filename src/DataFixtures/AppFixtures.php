<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Project;
use App\Entity\Status;
use App\Entity\Ticket;
use App\Entity\TicketType;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }
    public const COMPANY_REFERENCE = 'project';
    public const PROJECT_REFERENCE = 'project';
    public const USER_REFERENCE = 'user';
    public const TICKET_TYPE_REFERENCE = 'ticket_type';
    public const STATUS_REFERENCE = 'status';
    public function load(ObjectManager $manager): void
    {
        // Company Fixture
        $company = new Company();
        $company->setSubscription('free');
        $company->setName('ESGI Project');
        $manager->persist($company);
        $this->setReference(self::COMPANY_REFERENCE, $company);

        // Users Fixture
        $user = new User();
        $user->setEmail('admin@gmail.com');
        $user->setFirstname('John');
        $user->setLastname('Doe');
        $plainPassword = 'Azerty2023@';
        $password = $this->hasher->hashPassword($user, $plainPassword);
        $user->setPassword($password);
        $user->setCompany($this->getReference(AppFixtures::COMPANY_REFERENCE));
        $manager->persist($user);
        $this->setReference(self::USER_REFERENCE, $user);


        // Projects Fixture
        for ($i = 0; $i < 5; $i++) {
            $project = new Project();
            $project->setName("Projet n°" . $i);
            $project->setCompany($this->getReference(AppFixtures::COMPANY_REFERENCE));
            $manager->persist($project);
        }

        $this->setReference(self::PROJECT_REFERENCE, $project);

        // Ticket Type Fixture
        $type1 = new TicketType();
        $type2 = new TicketType();

        $type1->setName('Bug');
        $type2->setName('Evolution');

        $manager->persist($type1);
        $manager->persist($type2);

        $this->setReference(self::TICKET_TYPE_REFERENCE, $type2);

        // Status Fixture
        $statusTab = ['To be written', 'To do', 'Closed', 'New'];
        foreach ($statusTab as $status){
            $statusObject = new Status();
            $statusObject->setName($status);
            $manager->persist($statusObject);

        }

        $this->setReference(self::STATUS_REFERENCE, $statusObject);

        // Tickets Fixture
        for ($i = 0; $i < 25; $i++) {
            $ticket = new Ticket();
            $ticket->setTitle("Ticket " . $i);
            $ticket->setContent("Contenu du ticket n° " . $i);
            $ticket->setCreatedAt(new \DateTimeImmutable());
            $ticket->setUpdatedAt(new \DateTime());
            $ticket->setProject($this->getReference(AppFixtures::PROJECT_REFERENCE));
            $ticket->setAssignment($this->getReference(AppFixtures::USER_REFERENCE));
            $ticket->setType($this->getReference(AppFixtures::TICKET_TYPE_REFERENCE));
            $ticket->setStatus($this->getReference(AppFixtures::STATUS_REFERENCE));
            $manager->persist($ticket);
        }

        $manager->flush();
    }
}
