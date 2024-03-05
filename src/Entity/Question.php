<?php

namespace App\Entity;

use App\Repository\QuestionRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: QuestionRepository::class)]
class Question
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Please enter a Question.')]    
    private ?string $question = null;

    #[ORM\OneToMany(targetEntity: Reponse::class, mappedBy: 'question',cascade: ['persist', 'remove'])]

    private Collection $reponse;

    #[ORM\ManyToMany(targetEntity: Formulaire::class, mappedBy: 'Question')]
    private Collection $Reponse;

    #[ORM\ManyToMany(targetEntity: Formulaire::class, mappedBy: 'Question')]
    private Collection $formulaires;

    #[ORM\Column(type: Types::DATETIME_MUTABLE)]
    private ?\DateTimeInterface $date = null;

    #[ORM\Column]
    private ?bool $active =null;
    

    public function __construct()
    {
        $this->reponse = new ArrayCollection();
        $this->Reponse = new ArrayCollection();
        $this->formulaires = new ArrayCollection();
        $this->date = new \DateTime();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getQuestion(): ?string
    {
        return $this->question;
    }

    public function setQuestion(string $question): static
    {
        $this->question = $question;

        return $this;
    }

    /**
     * @return Collection<int, Reponse>
     */
    public function getReponse(): Collection
    {
        return $this->reponse;
    }

    public function addReponse(Reponse $reponse): static
    {
        if (!$this->reponse->contains($reponse)) {
            $this->reponse->add($reponse);
            $reponse->setQuestion($this);
        }

        return $this;
    }

    public function removeReponse(Reponse $reponse): static
    {
        if ($this->reponse->removeElement($reponse)) {
            // set the owning side to null (unless already changed)
            if ($reponse->getQuestion() === $this) {
                $reponse->setQuestion(null);
            }
        }

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
            $formulaire->addQuestion($this);
        }

        return $this;
    }

    public function removeFormulaire(Formulaire $formulaire): static
    {
        if ($this->formulaires->removeElement($formulaire)) {
            $formulaire->removeQuestion($this);
        }

        return $this;
    }

    public function getDate(): ?\DateTimeInterface
    {
        return $this->date;
    }

    public function setDate(\DateTimeInterface $date): static
    {
        $this->date = $date;

        return $this;
    }

    public function isActive(): ?bool
    {
        return $this->active;
    }

    public function setActive(bool $active): static
    {
        $this->active = $active;

        return $this;
    }
}
