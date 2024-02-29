<?php

namespace App\Entity;

use App\Repository\PanierRepository;
use Symfony\Component\Validator\Constraints as Assert;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PanierRepository::class)]
class Panier
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column]
    private ?float $PrixTot = null;

    #[ORM\Column]
    #[Assert\NotBlank(message:"The Quantity cannot be blank")]
    #[Assert\Type("integer")]
    #[Assert\GreaterThanOrEqual(value:0,message:"The Quantity must be positive")]
    private ?int $Quantity = null;

    #[ORM\ManyToOne]
    private ?Article $article = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getPrixTot(): ?float
    {
        return $this->PrixTot;
    }

    public function setPrixTot(float $PrixTot): static
    {
        $this->PrixTot = $PrixTot;

        return $this;
    }

    public function getQuantity(): ?int
    {
        return $this->Quantity;
    }

    public function setQuantity(int $Quantity): self
    {
        $this->Quantity = $Quantity;
        
        // Update PrixTot based on the new quantity and current article's price
        if ($this->article) {
            $this->PrixTot = $this->article->getPrix() * $this->Quantity;
        } else {
            $this->PrixTot = 0;
        }

        return $this;
    }

    public function getArticle(): ?Article
    {
        return $this->article;
    }

    public function setArticle(?Article $article): self
    {
        if ($article !== $this->article) {
            $this->article = $article;
            
            // Update PrixTot based on the new article's price and quantity
            $this->updatePrixTot();
        }

        return $this;
    }

    private function updatePrixTot(): void
    {
        if ($this->article) {
            $this->PrixTot = $this->article->getPrix() * $this->Quantity;
        } else {
            $this->PrixTot = 0;
        }
    }
    
    public function addArticle(Article $article): self
    {
        // Set the article
        $this->setArticle($article);

        // Update PrixTot by calculating total price
        $this->PrixTot += $article->getPrix() * $article->getQuantite();

        return $this;
    }

    public function removeArticle(): self
    {
        // Remove the article
        $this->setArticle(null);

        // Reset PrixTot
        $this->PrixTot = 0;

        return $this;
    }
    
    
    public function __construct()
    {
        // Initialize PrixTot to 0 in the constructor
        $this->PrixTot = 0;
    }
}
