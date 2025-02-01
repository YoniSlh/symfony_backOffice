<?php

namespace App\DataFixtures;

use App\Entity\Product;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class ProductFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $products = [
            ['name' => 'Vélo de route', 'description' => 'Un vélo rapide pour la route.', 'price' => 1200],
            ['name' => 'VTT tout terrain', 'description' => 'Idéal pour les chemins accidentés.', 'price' => 1500],
            ['name' => 'Vélo électrique', 'description' => 'Un vélo assisté pour les longues distances.', 'price' => 2500],
            ['name' => 'BMX freestyle', 'description' => 'Parfait pour les figures et le freestyle.', 'price' => 900],
            ['name' => 'Vélo pliant', 'description' => 'Compact et pratique pour la ville.', 'price' => 800],
        ];

        foreach ($products as $data) {
            $product = new Product();
            $product->setName($data['name']);
            $product->setDescription($data['description']);
            $product->setPrice($data['price']);

            $manager->persist($product);
        }

        $manager->flush();
    }
}
