<?php

namespace App\DataFixtures;

use App\Entity\Project;
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
    public const PROJECT_REFERENCE = 'project';
    public const USER_REFERENCE = 'user';

    public const TICKET_TYPE_REFERENCE = 'ticket_type';
    public function load(ObjectManager $manager): void
    {
        // Users Fixtures
        $user = new User();
        $user->setEmail('admin@gmail.com');
        $user->setFirstname('John');
        $user->setLastname('Doe');
        $plainPassword = 'Azerty2023@';
        $password = $this->hasher->hashPassword($user, $plainPassword);
        $user->setPassword($password);
        $manager->persist($user);
        $this->addReference(self::USER_REFERENCE, $user );

        // Projects Fixtures
        for ($i = 0; $i < 5; $i++) {
            $project = new Project();
            $project->setName("Projet n°" . $i);
            $manager->persist($project);
        }

        $this->addReference(self::PROJECT_REFERENCE, $project);
        // Ticket Type Fixture
        $type1 = new TicketType();
        $type2 = new TicketType();

        $type1->setName('Bug');
        $manager->persist($type1);

        $type2->setName('Evolution');
        $manager->persist($type2);
        $this->addReference(self::TICKET_TYPE_REFERENCE, $type2);


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
            $manager->persist($ticket);
        }

        $manager->flush();
    }
}
