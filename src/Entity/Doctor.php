<?php

namespace App\Entity;

use App\Repository\DoctorRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: DoctorRepository::class)]
class Doctor extends Entity
{
    public static function elementsToCreate(): array
    {
        return [
            'all' => ['subscription', 'area', 'name', 'specialty'],
            'required' => ['subscription', 'area', 'name', 'specialty'],
        ];
    }

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

    #[ORM\ManyToOne(targetEntity: Specialty::class, inversedBy: 'doctors')]
    #[ORM\JoinColumn(nullable: false)]
    private $specialty;

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

    public function getSpecialty(): ?Specialty
    {
        return $this->specialty;
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

    public function setSpecialty(?Specialty $specialty): self
    {
        $this->specialty = $specialty;

        return $this;
    }

    public function view(bool $showRelations = true): array
    {
        $id = $this->getId() ?? null;
        $name = $this->getName() ?? null;
        $area = $this->getArea() ?? null;
        $subscription = $this->getSubscription() ?? null;
        $specialtyId = $this->getSpecialty()->getId() ?? null;

        $view = [
            'Id' => $id,
            'Name' => $name,
            'Area' => $area,
            'Subscription' => $subscription,
            'SpecialtyId' => $specialtyId,
        ];

        if ($showRelations) {
            $specialtyRelation = [
                'rel' => 'Specialty',
                'path' => "/specialties/$specialtyId",
            ];

            $selfRelation = [
                'rel' => 'self',
                'path' => "/doctors/$id",
            ];

            $view['_links'] = [$specialtyRelation, $selfRelation];
        }

        return $view;
    }
}
