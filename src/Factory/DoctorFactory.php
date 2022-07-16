<?php

namespace App\Factory;

use App\Entity\Doctor;

class DoctorFactory extends Factory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected static function getClass(): string
    {
        return Doctor::class;
    }

    protected function getDefaults(): array
    {
        $defaults = [
            'subscription' => self::faker()->randomNumber(),
            'area' => self::faker()->countryCode(),
            'name' => self::faker()->name(),
            'specialty' => SpecialtyFactory::random()
        ];

        return $defaults;
    }
}
