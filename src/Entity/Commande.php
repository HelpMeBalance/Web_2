<?php

namespace App\Entity;

use App\Repository\CommandeRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CommandeRepository::class)]
class Commande
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Assert\Regex(
        pattern: '/^[a-zA-Z]+$/',
        message: 'Username must contain only letters.'
    )]    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $address = null;

    #[ORM\Column(length: 255)]
    private ?string $paymentmethode = null;

    #[ORM\Column(type: 'integer')]
    private ?int $status = 0; // Initialize status to 0 by default

    #[ORM\ManyToOne(inversedBy: 'commandes')]
    private ?User $user = null;

    #[ORM\ManyToMany(targetEntity:Article::class)]
    #[ORM\JoinTable(name: "commande_article")]
    private $articles;
    
    #[ORM\Column(type: 'float', nullable: true)]
    private ?float $totalPrice = null;

    #[ORM\ManyToMany(targetEntity:Panier::class, inversedBy: 'commandes')]
    private $paniers;

    #[ORM\Column(nullable: true)]
    private ?string $saleCode = null;


    public function __construct()
    {
        $this->status = false; // Initialize status to 0 by default
        $this->articles = new ArrayCollection();
        $this->paniers = new ArrayCollection(); // Initialize paniers to an empty collection
        $this->totalPrice = 0;

    }
    public function getSaleCode(): ?string
    {
    return $this->saleCode;
    }

    public function setSaleCode(?string $saleCode): self
    {
    $this->saleCode = $saleCode;

    return $this;
    }

    public function getStatus(): ?int
    {
        return $this->status;
    }

    public function setStatus(int $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): self
    {
        $this->username = $username;

        return $this;
    }

    public function getAddress(): ?string
    {
        return $this->address;
    }

    public function setAddress(string $address): self
    {
        $this->address = $address;

        return $this;
    }

    public function getPaymentmethode(): ?string
    {
        return $this->paymentmethode;
    }

    public function setPaymentmethode(string $paymentmethode): self
    {
        $this->paymentmethode = $paymentmethode;

        return $this;
    }

    public function getUser(): ?User
    {
        return $this->user;
    }

    public function setUser(?User $user): self
    {
        $this->user = $user;

        return $this;
    }
    public function getPaniers(): Collection
    {
        return $this->paniers;
    }

    public function getArticles(): Collection
    {
        return $this->articles;
    }


    public function getTotalPrice(): ?float
    {
        return $this->totalPrice;
    }

    public function addPanier(Panier $panier): self
    {
        if (!$this->paniers->contains($panier)) {
            $this->paniers[] = $panier;
        }

        return $this;
    }
    public function addArticle(Article $article): self
    {
        if (!$this->articles->contains($article)) {
            $this->articles[] = $article;
        }

        return $this;
    }

    public function removeArticle(Article $article): self
    {
        $this->articles->removeElement($article);

        return $this;
    }

    public function removePanier(Panier $panier): self
    {
        $this->paniers->removeElement($panier);

        return $this;
    }

    public function setTotalPrice(float $totalPrice): self
    {
        $this->totalPrice = $totalPrice;

        return $this;
    }
    public function addArticlesFromPaniers(Collection $paniers): void
{
    foreach ($paniers as $panier) {
        // Assuming $panier->getArticle() returns the associated Article entity
        $article = $panier->getArticle();
        $clonedArticle = clone $article; // Clone the article entity
        $this->articles->add($clonedArticle); // Add the cloned article to the collection
    }
    // Do not calculate total price here, as it should be calculated based on the paniers
}

/**
 * Calculate total price based on the articles associated with paniers of the commande.
 */
public function calculateTotalPrice(): void
    {
        $totalPrice = 0;
        foreach ($this->articles as $article) {
            // Assuming $article->getPrix() returns the price of the article
            $totalPrice += $article->getPrix() * $article->getQuantity();
        }
        $this->setTotalPrice($totalPrice);
    }
}
