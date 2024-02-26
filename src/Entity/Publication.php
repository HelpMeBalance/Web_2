<?php

namespace App\Entity;

use App\Repository\PublicationRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;
#[ORM\Entity(repositoryClass: PublicationRepository::class)]
class Publication
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    private ?string $contenu = null;

    #[ORM\Column]
    private ?bool $comOuvert = null;

    #[ORM\Column]
    private ?bool $anonyme = null;

    #[ORM\Column]
    private ?int $likes = 0;

    #[ORM\Column]
    private ?bool $valide = FALSE;

    #[ORM\Column(type: 'datetime_immutable')] //saisir date creation automatique
    private ?\DateTimeImmutable $dateC;

    #[ORM\Column(type: 'datetime_immutable')] 
    private ?\DateTimeImmutable $dateM;

    #[ORM\Column]
    private ?int $vues = 0;

    #[ORM\ManyToOne(inversedBy: 'publications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $User = null;

    #[ORM\ManyToOne(inversedBy: 'publications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Categorie $Categorie = null;

    #[ORM\ManyToOne(inversedBy: 'publications')]
    #[ORM\JoinColumn(nullable: false)]
    private ?SousCategorie $SousCategorie = null;

    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'Publication', cascade: ['persist', 'remove'])]
    private Collection $commentaires;

    #[ORM\Column(length: 255)]
    private ?string $titre = null;

    #[ORM\Column(length: 255)]
    private ?string $image = "";

    public function __construct()
    {
        $this->commentaires = new ArrayCollection();
        $this->dateC =  new \DateTimeImmutable();
        $this->dateM = new \DateTimeImmutable();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getContenu(): ?string
    {
        return $this->contenu;
    }

    public function setContenu(string $contenu): static
    {
        $this->contenu = $contenu;

        return $this;
    }

    public function isComOuvert(): ?bool
    {
        return $this->comOuvert;
    }

    public function setComOuvert(bool $comOuvert): static
    {
        $this->comOuvert = $comOuvert;

        return $this;
    }

    public function isAnonyme(): ?bool
    {
        return $this->anonyme;
    }

    public function setAnonyme(bool $anonyme): static
    {
        $this->anonyme = $anonyme;

        return $this;
    }

    public function getLikes(): ?int
    {
        return $this->likes;
    }

    public function setLikes(int $likes): static
    {
        $this->likes = $likes;

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

    public function getDateC(): ?\DateTimeImmutable
    {
        return $this->dateC;
    }

    public function setDateC(\DateTimeImmutable $dateC): static
    {
        $this->dateC = $dateC;

        return $this;
    }

    public function getDateM(): ?\DateTimeImmutable
    {
        return $this->dateM;
    }

    public function setDateM(\DateTimeImmutable $dateM): static
    {
        $this->dateM = $dateM;

        return $this;
    }

    public function getVues(): ?int
    {
        return $this->vues;
    }

    public function setVues(int $vues): static
    {
        $this->vues = $vues;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }

    public function getCategorie(): ?Categorie
    {
        return $this->Categorie;
    }

    public function setCategorie(?Categorie $Categorie): static
    {
        $this->Categorie = $Categorie;

        return $this;
    }

    public function getSousCategorie(): ?SousCategorie
    {
        return $this->SousCategorie;
    }

    public function setSousCategorie(?SousCategorie $SousCategorie): static
    {
        $this->SousCategorie = $SousCategorie;

        return $this;
    }

    /**
     * @return Collection<int, Commentaire>
     */
    public function getCommentaires(): Collection
    {
        return $this->commentaires;
    }

    public function addCommentaire(Commentaire $commentaire): static
    {
        if (!$this->commentaires->contains($commentaire)) {
            $this->commentaires->add($commentaire);
            $commentaire->setPublication($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getPublication() === $this) {
                $commentaire->setPublication(null);
            }
        }

        return $this;
    }

    public function getTitre(): ?string
    {
        return $this->titre;
    }

    public function setTitre(string $titre): static
    {
        $this->titre = $titre;

        return $this;
    }

    public function getImage(): ?string
    {
        return $this->image;
    }

    public function setImage(string $image): static
    {
        $this->image = $image;

        return $this;
    }
}
