<?php

namespace App\Factory;

use App\Entity\Entity;

interface Factory
{
    public function createEntity(array $data): Entity|false;
}
