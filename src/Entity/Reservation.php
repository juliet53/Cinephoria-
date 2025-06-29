<?php

namespace App\Entity;

use App\Repository\ReservationRepository;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;

use Symfony\Component\Serializer\Annotation\Groups;
use ApiPlatform\Metadata\ApiFilter;
use ApiPlatform\Doctrine\Orm\Filter\SearchFilter;

#[ORM\Entity(repositoryClass: ReservationRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['reservation:read']],
    paginationEnabled: false
)]
#[ApiFilter(SearchFilter::class, properties: ['user' => 'exact'])]
class Reservation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(['reservation:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['reservation:read'])]
    private ?int $placeReserve = null;

    #[ORM\Column]
    #[Groups(['reservation:read'])]
    private ?float $prix = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[Groups(['reservation:read'])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'reservations')]
    #[Groups(['reservation:read'])]
    private ?Seance $seance = null;

    #[ORM\Column(type: 'json', nullable: true)]
    #[Groups(['reservation:read'])]
    private ?array $seats = [];

    #[ORM\Column(type: 'string', nullable: true)]
    #[Groups(['reservation:read'])]
    private ?string $qrCodePath = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPlaceReserve(): ?int
    {
        return $this->placeReserve;
    }

    public function setPlaceReserve(int $placeReserve): static
    {
        $this->placeReserve = $placeReserve;
        return $this;
    }

    public function getPrix(): ?float
    {
        return $this->prix;
    }

    public function setPrix(float $prix): static
    {
        $this->prix = $prix;
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

    public function getSeance(): ?Seance
    {
        return $this->seance;
    }

    public function setSeance(?Seance $seance): static
    {
        $this->seance = $seance;
        return $this;
    }

    public function getSeats(): ?array
    {
        return $this->seats;
    }

    public function setSeats(?array $seats): static
    {
        $this->seats = $seats;
        return $this;
    }

    public function getQrCodePath(): ?string
    {
        return $this->qrCodePath;
    }

    public function setQrCodePath(?string $qrCodePath): static
    {
        $this->qrCodePath = $qrCodePath;
        return $this;
    }
}
