<?php

namespace App\Entity;

use App\Repository\RendezVousRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: RendezVousRepository::class)]
class RendezVous
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[Assert\NotBlank(message: 'Please enter a date')]    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $dateR = null;

    #[Assert\NotBlank(message: 'Please enter a Service Name')]
    #[ORM\Column(length: 255)]
    private ?string $nomService = null;

    #[ORM\Column]
    private ?bool $statut = null;

    #[ORM\ManyToOne(inversedBy: 'rendezVouses')]
    #[Assert\NotBlank(message: 'Please select a psychayatrist')]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'rendezVousesP')]
    private ?User $patient = null;

    #[ORM\OneToMany(mappedBy: 'RendezVous', targetEntity: Formulaire::class)]
    private Collection $formulaires;

    #[ORM\Column]
    private ?bool $certificat = null;

    public function __construct()
    {
        $this->formulaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): static
    {
        $this->id = $id;

        return $this;
    }

    public function getDateR(): ?\DateTimeInterface
    {
        return $this->dateR;
    }

    public function setDateR(\DateTimeInterface $dateR): static
    {
        $this->dateR = $dateR;

        return $this;
    }

    public function getNomService(): ?string
    {
        return $this->nomService;
    }

    public function setNomService(string $nomService): static
    {
        $this->nomService = $nomService;

        return $this;
    }

    public function isStatut(): ?bool
    {
        return $this->statut;
    }

    public function setStatut(bool $statut): static
    {
        $this->statut = $statut;

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

    public function getPatient(): ?User
    {
        return $this->patient;
    }

    public function setPatient(?User $patient): static
    {
        $this->patient = $patient;

        return $this;
    }

    /**
     * @return Collection<int, Formulaire>
     */
    public function getFormulaires(): Collection
    {
        return $this->formulaires;
    }

    public function addFormulaire(Formulaire $formulaire): static
    {
        if (!$this->formulaires->contains($formulaire)) {
            $this->formulaires->add($formulaire);
            $formulaire->setRendezVous($this);
        }

        return $this;
    }

    public function removeFormulaire(Formulaire $formulaire): static
    {
        if ($this->formulaires->removeElement($formulaire)) {
            // set the owning side to null (unless already changed)
            if ($formulaire->getRendezVous() === $this) {
                $formulaire->setRendezVous(null);
            }
        }

        return $this;
    }

    public function isCertificat(): ?bool
    {
        return $this->certificat;
    }

    public function setCertificat(bool $certificat): static
    {
        $this->certificat = $certificat;

        return $this;
    }
}