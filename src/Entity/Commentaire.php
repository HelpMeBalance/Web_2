<?php

namespace App\Entity;

use App\Repository\CommentaireRepository;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Validator\Constraints as Assert;

#[ORM\Entity(repositoryClass: CommentaireRepository::class)]
class Commentaire
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::TEXT)]
    #[Assert\NotBlank(message: 'Please enter the content of your comment.')]
    #[Assert\Length([
        'min' => 3,
        'max' => 200,
        'minMessage' => 'Your comment must be at least {{ limit }} characters long',
        'maxMessage' => 'Your comment cannot be longer than {{ limit }} characters',
    ])]
    #[Assert\Regex(
        pattern: '/[A-Za-z]/',
        message: 'Your comment must contain at least one letter.'
    )]
    private ?string $contenu = null;

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

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $User = null;

    #[ORM\ManyToOne(inversedBy: 'commentaires')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Publication $Publication = null;

    public function __construct()
    {
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

    public function setContenu(string $contenu= null): static
    {
        $this->contenu = $contenu;

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

    public function getUser(): ?User
    {
        return $this->User;
    }

    public function setUser(?User $User): static
    {
        $this->User = $User;

        return $this;
    }

    public function getPublication(): ?Publication
    {
        return $this->Publication;
    }

    public function setPublication(?Publication $Publication): static
    {
        $this->Publication = $Publication;

        return $this;
    }
}
