<?php

namespace App\Factory;

use App\Entity\Specialty;

class SpecialtyFactory extends Factory
{
    protected static function getClass(): string
    {
        return Specialty::class;
    }

    protected function getDefaults(): array
    {
        return [];
    }

    public function updateEntity()
    {
        echo 'Refatoring...';
    }
}
