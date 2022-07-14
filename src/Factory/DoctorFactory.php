<?php

namespace App\Factory;

use App\Entity\Doctor;

class DoctorFactory extends Factory
{
    protected static function getClass(): string
    {
        return Doctor::class;
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
