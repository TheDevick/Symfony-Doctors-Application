<?php

namespace App\DataFixtures;

use App\Factory\DoctorFactory;
use App\Factory\SpecialtyFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        SpecialtyFactory::createMany(20);
        DoctorFactory::createMany(20);

        // $product = new Product();
        // $manager->persist($product);

        // $manager->flush();
    }
}
