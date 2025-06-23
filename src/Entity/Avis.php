<?php

namespace App\Entity;

use App\Repository\AvisRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;
use Symfony\Component\Serializer\Annotation\MaxDepth;

#[ORM\Entity(repositoryClass: AvisRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['avis:read']],
    denormalizationContext: ['groups' => ['avis:write']]
)]
class Avis
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['avis:read', 'film:read'])]
    private ?int $id = null;

    #[ORM\Column(nullable: true)]
    #[Groups(['avis:read', 'avis:write', 'film:read'])]
    private ?float $note = null;

    #[ORM\ManyToOne(inversedBy: 'avis')]
    #[Groups(['avis:read', 'avis:write'])]
    #[MaxDepth(1)]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'avis')]
    #[Groups(['avis:read', 'avis:write'])] // Exclure 'film:read' pour éviter la boucle
    #[MaxDepth(1)]
    private ?Film $film = null;

    #[ORM\Column(type: Types::TEXT, nullable: true)]
    #[Groups(['avis:read', 'avis:write', 'film:read'])]
    private ?string $Commentaire = null;

    #[ORM\Column]
    #[Groups(['avis:read', 'avis:write', 'film:read'])]
    private ?bool $valide = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNote(): ?float
    {
        return $this->note;
    }

    public function setNote(?float $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): static
    {
        $this->user = $user;

        return $this;
    }

    public function getFilm(): ?Film
    {
        return $this->film;
    }

    public function setFilm(?Film $film): static
    {
        $this->film = $film;

        return $this;
    }

    public function getCommentaire(): ?string
    {
        return $this->Commentaire;
    }

    public function setCommentaire(?string $Commentaire): static
    {
        $this->Commentaire = $Commentaire;

        return $this;
    }

    public function isValide(): ?bool
    {
        return $this->valide;
    }

    public function setValide(bool $valide): static
    {
        $this->valide = $valide;

        return $this;
    }
}