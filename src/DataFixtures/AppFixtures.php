<?php

namespace App\DataFixtures;

use App\Entity\Project;
use App\Entity\Ticket;
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
    public function load(ObjectManager $manager): void
    {
        $user = new User();
        $user->setEmail('admin@gmail.com');
        $plainPassword = 'Azerty2023@';
        $password = $this->hasher->hashPassword($user, $plainPassword);
        $user->setPassword($password);
        $manager->persist($user);
        $this->addReference(self::USER_REFERENCE, $user );

        for ($i = 0; $i < 5; $i++) {
            $project = new Project();
            $project->setName("Projet n°" . $i);
            $manager->persist($project);
        }

        $this->addReference(self::PROJECT_REFERENCE, $project);

        for ($i = 0; $i < 25; $i++) {
            $ticket = new Ticket();
            $ticket->setTitle("Ticket " . $i);
            $ticket->setContent("Contenu du ticket n° " . $i);
            $ticket->setCreatedAt(new \DateTimeImmutable());
            $ticket->setUpdatedAt(new \DateTime());
            $ticket->setProject($this->getReference(AppFixtures::PROJECT_REFERENCE));
            $ticket->setAssignment($this->getReference(AppFixtures::USER_REFERENCE));
            $manager->persist($ticket);
        }

        $manager->flush();
    }
}
