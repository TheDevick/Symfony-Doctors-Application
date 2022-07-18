<?php

namespace App\Repository;

use App\Entity\Specialty;
use Doctrine\Persistence\ManagerRegistry;

class SpecialtyRepository extends Repository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Specialty::class);
    }
}
