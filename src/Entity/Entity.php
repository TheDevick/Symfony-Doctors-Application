<?php

namespace App\Entity;

abstract class Entity implements \JsonSerializable
{
    abstract public static function elementsToCreate(): array;

    abstract public function getId(): ?int;

    abstract public function view(): array;

    public function jsonSerialize(): mixed
    {
        return $this->view();
    }
}
