<?php

namespace App\Repository;

use App\Entity\Entity;

interface Repository
{
    public function findAll();

    public function find($id, $lockMode = null, $lockVersion = null);

    public function add(Entity $entity, bool $flush = false): void;

    public function remove(Entity $entity, bool $flush = false): void;

    public function flush(): void;
}
