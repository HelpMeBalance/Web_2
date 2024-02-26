<?php

namespace App\Entity;

use App\Repository\ConsultationRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Mime\Message;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ConsultationRepository::class)]
class Consultation
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TIME_MUTABLE)]
    #[Assert\NotBlank(message: 'veuillez remplir durée.')]
    private ?\DateTimeInterface $duree = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'veuillez remplir note.')]
    private ?string $note = null;

    #[ORM\Column(nullable: true)]
    #[Assert\NotBlank(message: 'veuillez remplir avis patient.')]
    #[Assert\Range(min:0, max:5, notInRangeMessage:'veuillez entrer une valeur entre 0 et 5')]
    private ?float $avisPatient = null;

    #[ORM\Column]
    private ?bool $RecommandationSuivi = null;

    #[ORM\OneToOne(cascade: ['persist', 'remove'])]
    #[Assert\NotBlank(message: 'veuillez remplir rendez-vous.')]
    private ?RendezVous $rendezvous = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDuree(): ?\DateTimeInterface
    {
        return $this->duree;
    }

    public function setDuree(\DateTimeInterface $duree): static
    {
        $this->duree = $duree;

        return $this;
    }

    public function getNote(): ?string
    {
        return $this->note;
    }

    public function setNote(string $note): static
    {
        $this->note = $note;

        return $this;
    }

    public function getAvisPatient(): ?float
    {
        return $this->avisPatient;
    }

    public function setAvisPatient(?float $avisPatient): static
    {
        $this->avisPatient = $avisPatient;

        return $this;
    }

    public function isRecommandationSuivi(): ?bool
    {
        return $this->RecommandationSuivi;
    }

    public function setRecommandationSuivi(bool $RecommandationSuivi): static
    {
        $this->RecommandationSuivi = $RecommandationSuivi;

        return $this;
    }

    public function getRendezvous(): ?RendezVous
    {
        return $this->rendezvous;
    }

    public function setRendezvous(?RendezVous $rendezvous): static
    {
        $this->rendezvous = $rendezvous;

        return $this;
    }
}
