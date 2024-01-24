<?php

namespace App\DataFixtures;

use App\Entity\Company;
use App\Entity\Project;
use App\Entity\Status;
use App\Entity\Subscription;
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
    public const SUBSCRIPTION_REFERENCE = 'subscription';
    public const COMPANY_REFERENCE = 'project';
    public const PROJECT_REFERENCE = 'project';
    public const USER_REFERENCE = 'user';
    public const TICKET_TYPE_REFERENCE = 'ticket_type';
    public const STATUS_REFERENCE = 'status';
    public function load(ObjectManager $manager): void
    {
        // Subscription Fixture
        $subscriptionNames = ['Entreprise', 'Pro', 'Free'];
        $subscriptionPrices = [2999, 1499,0];
        $subscriptionMaxUsers = [100, 10, 1];
        $subscriptionMaxProjects = [100, 3, 1];
        $subscriptionStripePriceId = [
            'price_1OMsaQJNd8Md8wujoIt8Ewfr',
            'price_1OMsZlJNd8Md8wujJPHGCi4z',
            ''
        ];

        for ($i = 0; $i < 3; $i++) {
            $subscription = new Subscription();
            $subscription->setName($subscriptionNames[$i]);
            $subscription->setPrice($subscriptionPrices[$i]);
            $subscription->setMaxUsers($subscriptionMaxUsers[$i]);
            $subscription->setMaxProjects($subscriptionMaxProjects[$i]);
            $subscription->setStripeSubscriptionId($subscriptionStripePriceId[$i]);
            $manager->persist($subscription);
            $this->setReference(self::SUBSCRIPTION_REFERENCE, $subscription);
        }

        // Company Fixture
        $company = new Company();
        $company->setSubscription($this->getReference(AppFixtures::SUBSCRIPTION_REFERENCE));
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

        $project = new Project();
        $project->setName("Projet n°1");
        $project->setIsPublic(true);
        $project->setCompany($this->getReference(AppFixtures::COMPANY_REFERENCE));
        $manager->persist($project);


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
