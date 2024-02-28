<?php

namespace App\Entity;

use App\Repository\FormulaireRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: FormulaireRepository::class)]
class Formulaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToMany(targetEntity: Question::class, inversedBy: 'formulaires')]

    private Collection $Question;

    #[ORM\ManyToMany(targetEntity: Reponse::class, inversedBy: 'formulaires')]
    #[Assert\Count(min: 1, minMessage: 'Please select at least one response.')]
    //#[Assert\Collection(allowMissingFields: false)]
    private Collection $Reponse;

    #[ORM\OneToOne(inversedBy: 'formulaire', cascade: ['persist', 'remove'])]
    private ?User $user = null;

    public function __construct()
    {
        $this->Question = new ArrayCollection();
        $this->Reponse = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, Question>
     */
    public function getQuestion(): Collection
    {
        return $this->Question;
    }

    public function addQuestion(Question $question): static
    {
        if (!$this->Question->contains($question)) {
            $this->Question->add($question);
        }

        return $this;
    }

    public function removeQuestion(Question $question): static
    {
        $this->Question->removeElement($question);

        return $this;
    }

    /**
     * @return Collection<int, Reponse>
     */
    public function getReponse(): Collection
    {
        return $this->Reponse;
    }

    public function addReponse(Reponse $reponse): static
    {
        if (!$this->Reponse->contains($reponse)) {
            $this->Reponse->add($reponse);
        }

        return $this;
    }

    public function removeReponse(Reponse $reponse): static
    {
        $this->Reponse->removeElement($reponse);

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
}
