<?php

namespace App\Entity;

use App\Repository\SalleRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity(repositoryClass: SalleRepository::class)]
#[ApiResource(
    normalizationContext: ['groups' => ['salle:read']]
)]

class Salle
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
   #[Groups(['reservation:read', 'incident:read'])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(['reservation:read', 'incident:read', 'salle:read'])]
    private ?int $numero = null;

    #[ORM\Column]
    private ?int $capacite = null;

    #[ORM\Column(length: 255, nullable: true)]
    private ?string $qualite = null;

    /**
     * @var Collection<int, Seance>
     */
    #[ORM\OneToMany(targetEntity: Seance::class, mappedBy: 'salle')]
    private Collection $seances;

    #[ORM\ManyToOne(inversedBy: 'salle')]
    #[Groups(['incident:read', 'salle:read'])]
    private ?Cinema $cinema = null;

    /**
     * @var Collection<int, Incident>
     */
    #[ORM\OneToMany(targetEntity: Incident::class, mappedBy: 'salle')]
    private Collection $incidents;

    public function __construct()
    {
        $this->seances = new ArrayCollection();
        $this->incidents = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getNumero(): ?int
    {
        return $this->numero;
    }

    public function setNumero(int $numero): static
    {
        $this->numero = $numero;

        return $this;
    }

    public function getCapacite(): ?int
    {
        return $this->capacite;
    }

    public function setCapacite(int $capacite): static
    {
        $this->capacite = $capacite;

        return $this;
    }

    public function getQualite(): ?string
    {
        return $this->qualite;
    }

    public function setQualite(string $qualite): static
    {
        $this->qualite = $qualite;

        return $this;
    }

    /**
     * @return Collection<int, Seance>
     */
    public function getSeances(): Collection
    {
        return $this->seances;
    }

    public function addSeance(Seance $seance): static
    {
        if (!$this->seances->contains($seance)) {
            $this->seances->add($seance);
            $seance->setSalle($this);
        }

        return $this;
    }

    public function removeSeance(Seance $seance): static
    {
        if ($this->seances->removeElement($seance)) {
            // set the owning side to null (unless already changed)
            if ($seance->getSalle() === $this) {
                $seance->setSalle(null);
            }
        }

        return $this;
    }

    public function getCinema(): ?Cinema
    {
        return $this->cinema;
    }

    public function setCinema(?Cinema $cinema): static
    {
        $this->cinema = $cinema;

        return $this;
    }

    /**
     * @return Collection<int, Incident>
     */
    public function getIncidents(): Collection
    {
        return $this->incidents;
    }

    public function addIncident(Incident $incident): static
    {
        if (!$this->incidents->contains($incident)) {
            $this->incidents->add($incident);
            $incident->setSalle($this);
        }

        return $this;
    }

    public function removeIncident(Incident $incident): static
    {
        if ($this->incidents->removeElement($incident)) {
            // set the owning side to null (unless already changed)
            if ($incident->getSalle() === $this) {
                $incident->setSalle(null);
            }
        }

        return $this;
    }
}
