<?php

namespace App\Entity;

use ApiPlatform\Metadata\ApiResource;
use App\Repository\IncidentRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: IncidentRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['incident:read']]
)]
class Incident
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['incident:read'])]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Groups(['incident:read'])]
    private ?string $description = null;

    #[ORM\Column(length: 255)]
    #[Groups(['incident:read'])]
    private ?string $statut = null;

    #[ORM\ManyToOne(inversedBy: 'incidents')]
    #[ORM\JoinColumn(nullable: false)]
    #[Groups(['incident:read'])]
    private ?Salle $salle = null;  

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        $cleanDescription = strip_tags($description);
        $this->description = $cleanDescription;

        return $this;
    }

    public function getStatut(): ?string
    {
        return $this->statut;
    }

    public function setStatut(string $statut): static
    {
        $this->statut = $statut;

        return $this;
    }

    public function getSalle(): ?Salle  
    {
        return $this->salle;
    }

    public function setSalle(?Salle $salle): static  
    {
        $this->salle = $salle;

        return $this;
    }
}
