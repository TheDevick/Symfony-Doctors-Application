<?php

namespace App\Repository;

use App\Entity\Entity;
use App\Exception\JsonNotFoundException;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ObjectRepository;

abstract class Repository extends ServiceEntityRepository implements ObjectRepository
{
    public function findById(int $id, bool $throwException = true): Entity|null
    {
        $entity = $this->find($id);

        if (is_null($entity) && $throwException) {
            throw new JsonNotFoundException();
        }

        return $entity;
    }

    public function add(Entity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->persist($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function remove(Entity $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function flush(): void
    {
        $this->getEntityManager()->flush();
    }
}
