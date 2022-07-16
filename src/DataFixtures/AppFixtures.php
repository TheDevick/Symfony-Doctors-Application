<?php

namespace App\DataFixtures;

use App\Factory\DoctorFactory;
use App\Factory\SpecialtyFactory;
use App\Factory\UserFactory;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;

class AppFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        SpecialtyFactory::createMany(20);
        DoctorFactory::createMany(20);
        UserFactory::createMany(5);
        UserFactory::createOne([
            'password' => '123456',
            'email' => 'default@user.com',
        ]);
    }
}
