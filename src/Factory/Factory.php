<?php

namespace App\Factory;

use App\Entity\Entity;

interface Factory
{
    public function createEntity(array $data): Entity|false;
    public function updateEntity(Entity $entity, array $data): Entity|false;
}
