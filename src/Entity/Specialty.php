<?php

namespace App\Entity;

use App\Repository\SpecialtyRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: SpecialtyRepository::class)]
class Specialty extends Entity
{
    public static function elementsToCreate(): array
    {
        return [
            'all' => ['title', 'description'],
            'required' => ['title'],
            'unrequired' => ['description'],
        ];
    }

    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column(type: 'integer')]
    private $id;

    #[ORM\Column(type: 'string', length: 50)]
    private $title;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $description;

    #[ORM\OneToMany(mappedBy: 'specialty', targetEntity: Doctor::class)]
    private $doctors;

    public function __construct()
    {
        $this->doctors = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function getDoctors(): Collection
    {
        return $this->doctors;
    }

    public function setTitle(string $title): self
    {
        $this->title = $title;

        return $this;
    }

    public function setDescription(?string $description): self
    {
        $this->description = $description;

        return $this;
    }

    public function addDoctor(Doctor $doctor): self
    {
        if (!$this->doctors->contains($doctor)) {
            $this->doctors[] = $doctor;
            $doctor->setSpecialty($this);
        }

        return $this;
    }

    public function removeDoctor(Doctor $doctor): self
    {
        if ($this->doctors->removeElement($doctor)) {
            // set the owning side to null (unless already changed)
            if ($doctor->getSpecialty() === $this) {
                $doctor->setSpecialty(null);
            }
        }

        return $this;
    }

    public function view(bool $showRelations = true): array
    {
        $id = $this->getId() ?? null;
        $title = $this->getTitle() ?? null;
        $description = $this->getDescription() ?? null;

        $view = [
            'Id' => $id,
            'Title' => $title,
            'Description' => $description,
        ];

        if ($showRelations) {
            $doctorRelation = [
                'rel' => 'Doctors',
                'path' => "/specialties/$id/doctors",
            ];

            $selfRelation = [
                'rel' => 'self',
                'path' => "/specialties/$id",
            ];

            $view['_links'] = [$doctorRelation, $selfRelation];
        }

        return $view;
    }
}
