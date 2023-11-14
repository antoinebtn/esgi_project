<?php

namespace App\DataFixtures;

use App\Entity\Ticket;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
;

class TicketFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {

        for ($i = 0; $i < 25; $i++) {
            $ticket = new Ticket();
            $ticket->setTitle("Ticket " . $i);
            $ticket->setContent("Contenu du ticket n° " . $i);
            $ticket->setCreatedAt(new \DateTimeImmutable());
            $ticket->setUpdatedAt(new \DateTime());
            $manager->persist($ticket);
        }

        $manager->flush();
    }
}
