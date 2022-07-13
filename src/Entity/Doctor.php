<?php

namespace App\Entity;

use App\Repository\DoctorRepository;
use Doctrine\ORM\Mapping as ORM;
use JsonSerializable;

#[ORM\Entity(repositoryClass: DoctorRepository::class)]
class Doctor implements JsonSerializable
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'integer')]
    private $subscription;

    #[ORM\Column(type: 'string', length: 2)]
    private $area;

    #[ORM\Column(type: 'string', length: 50)]
    private $name;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSubscription(): ?int
    {
        return $this->subscription;
    }

    public function getArea(): ?string
    {
        return $this->area;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setSubscription(int $subscription): self
    {
        $this->subscription = $subscription;

        return $this;
    }

    public function setArea(string $area): self
    {
        $this->area = $area;

        return $this;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    public function view(): array
    {
        $id = $this->getId() ?? null;
        $name = $this->getName() ?? null;
        $area = $this->getArea() ?? null;
        $subscription = $this->getSubscription() ?? null;

        return [
            'Id' => $id,
            'Name' => $name,
            'Area' => $area,
            'Subscription' => $subscription,
        ];
    }

    public function jsonSerialize(): mixed
    {
        return $this->view();
    }
}
