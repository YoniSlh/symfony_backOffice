<?php

namespace App\DataFixtures;

use App\Entity\Client;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ClientFixtures extends Fixture
{
    public function load(ObjectManager $manager)
    {
        $client = new Client();
        $client->setFirstname('Mary-Jane')
            ->setLastname('Collins')
            ->setEmail('user@example.com')
            ->setPhoneNumber('1234567890')
            ->setAddress('123 Main St, City, Country')
            ->setRole('ROLE_USER')
            ->setCreatedAt(new \DateTimeImmutable());

        $manager->persist($client);
        $manager->flush();
    }
}
