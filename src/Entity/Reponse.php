<?php

namespace App\Entity;

use App\Repository\ReponseRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: ReponseRepository::class)]
class Reponse
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\NotBlank(message: 'Please enter a Reponse.')]
    private ?string $reponse = null;

    #[ORM\ManyToOne(inversedBy: 'reponse')]
    #[Assert\NotBlank(message: 'Please enter a question.')]
    private ?Question $question = null;

    #[ORM\ManyToMany(targetEntity: Formulaire::class, mappedBy: 'Reponse')]
    private Collection $formulaires;

    public function __construct()
    {
        $this->formulaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getReponse(): ?string
    {
        return $this->reponse;
    }

    public function setReponse(string $reponse): static
    {
        $this->reponse = $reponse;

        return $this;
    }

    public function getQuestion(): ?Question
    {
        return $this->question;
    }

    public function setQuestion(?Question $question): static
    {
        $this->question = $question;

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
            $formulaire->addReponse($this);
        }

        return $this;
    }

    public function removeFormulaire(Formulaire $formulaire): static
    {
        if ($this->formulaires->removeElement($formulaire)) {
            $formulaire->removeReponse($this);
        }

        return $this;
    }
}
