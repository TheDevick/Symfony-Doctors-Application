<?php

namespace App\Repository;

use App\Entity\Entity;
use Doctrine\Persistence\ObjectRepository;

interface Repository extends ObjectRepository
{
    public function add(Entity $entity, bool $flush = false): void;
    public function remove(Entity $entity, bool $flush = false): void;
    public function flush(): void;
}
