<?php

namespace App\Factory;

use App\Entity\Specialty;

class SpecialtyFactory extends Factory
{
    public function __construct()
    {
        parent::__construct();
    }

    protected static function getClass(): string
    {
        return Specialty::class;
    }

    protected function getDefaults(): array
    {
        $defaults = [
            'title' => self::faker()->jobTitle(),
            'description' => self::faker()->text(20),
        ];

        return $defaults;
    }
}
