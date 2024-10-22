<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;
use Doctrine\Common\Collections\ArrayCollection;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\HttpFoundation\File\File; // Add this line
use DateTimeInterface;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[UniqueEntity(fields: ['email'], message: 'There is already an account with this email')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 180, unique: true)]
    private ?string $email = null;

    #[ORM\Column]
    private array $roles = [];

    /**
     * @var string The hashed password
     */
    #[ORM\Column]
    private ?string $password = null;

    #[ORM\OneToMany(targetEntity: Publication::class, mappedBy: 'User', cascade: ['persist', 'remove'])]
    private Collection $publications;

    #[ORM\OneToMany(targetEntity: Commentaire::class, mappedBy: 'User', cascade: ['persist', 'remove'])]
    private Collection $commentaires;

    #[ORM\Column(length: 100)]
    // #[Assert\NotBlank(message: 'Please enter your first name.')]
    private ?string $firstname = null;

    #[ORM\Column(length: 100)]
    // #[Assert\NotBlank(message: 'Please enter your last name.')]
    private ?string $lastname = null;

    #[ORM\OneToMany(targetEntity: Consultation::class, mappedBy: 'Patient')]
    private Collection $consultations;

    #[ORM\OneToMany(targetEntity: RendezVous::class, mappedBy: 'user')]
    private Collection $rendezVouses;

    #[ORM\OneToMany(targetEntity: RendezVous::class, mappedBy: 'patient')]
    private Collection $rendezVousesP;
     #[ORM\OneToMany(mappedBy: 'user', targetEntity: Commande::class)]
    private Collection $commandes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Panier::class)]
    private Collection $paniers;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private ?string $profilePicture = null;

    // Temporary store the file in the object
    #[Assert\File(
        maxSize: '1024k',
        mimeTypes: ['image/jpeg', 'image/png'],
    )]
    
    private ?File $profilePictureFile = null;

    #[ORM\Column(type: 'string', length: 255, nullable: true)]
    private $googleId;

    public function getGoogleId(): ?string
    {
        return $this->googleId;
    }

    public function setGoogleId(?string $googleId): self
    {
        $this->googleId = $googleId;

        return $this;
    }


    public function getProfilePicture(): ?string
    {
        return $this->profilePicture;
    }

    public function setProfilePicture(?string $profilePicture): self
    {
        $this->profilePicture = $profilePicture;
        return $this;
    }

    public function getProfilePictureFile(): ?File
    {
        return $this->profilePictureFile;
    }

    public function setProfilePictureFile(?File $profilePictureFile = null): self
    {
        $this->profilePictureFile = $profilePictureFile;
        if ($profilePictureFile) {
            // It is required that at least one field changes if you are using doctrine
            // otherwise the event listeners won't be called and the file is lost
            // Update an "updatedAt" field here, if you have one
            $this->updatedAt = new \DateTimeImmutable();
        }
        return $this;
    }
        

    public function __construct()
    {
        $this->publications = new ArrayCollection();
        $this->commentaires = new ArrayCollection();
        $this->rendezVousesP = new ArrayCollection();
        $this->createdAt = new \DateTimeImmutable();
        $this->roles = ['ROLE_USER'];
        $this->paniers = new ArrayCollection();
        $this->commandes = new ArrayCollection();
        $this->likes = new ArrayCollection();
        $this->formulaires = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    /**
     * A visual identifier that represents this user.
     *
     * @see UserInterface
     */
    public function getUserIdentifier(): string
    {
        return (string) $this->email;
    }

    /**
     * @deprecated since Symfony 5.3, use getUserIdentifier instead
     */
    public function getUsername(): string
    {
        return (string) $this->email;
    }

    /**
     * @see UserInterface
     */
    public function getRoles(): array
    {
        $roles = $this->roles;
        // guarantee every user at least has ROLE_USER
        $roles[] = 'ROLE_USER';

        return array_unique($roles);
    }

    public function setRoles(array $roles): static
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * @see PasswordAuthenticatedUserInterface
     */
    public function getPassword(): string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    /**
     * Returning a salt is only needed, if you are not using a modern
     * hashing algorithm (e.g. bcrypt or sodium) in your security.yaml.
     *
     * @see UserInterface
     */
    public function getSalt(): ?string
    {
        return null;
    }

    /**
     * @see UserInterface
     */
    public function eraseCredentials(): void
    {
        // If you store any temporary, sensitive data on the user, clear it here
        // $this->plainPassword = null;
    }

    /**
     * @return Collection<int, Publication>
     */
    public function getPublications(): Collection
    {
        return $this->publications;
    }

    public function addPublication(Publication $publication): static
    {
        if (!$this->publications->contains($publication)) {
            $this->publications->add($publication);
            $publication->setUser($this);
        }

        return $this;
    }

    public function removePublication(Publication $publication): static
    {
        if ($this->publications->removeElement($publication)) {
            // set the owning side to null (unless already changed)
            if ($publication->getUser() === $this) {
                $publication->setUser(null);
            }
        }

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
            $commentaire->setUser($this);
        }

        return $this;
    }

    public function removeCommentaire(Commentaire $commentaire): static
    {
        if ($this->commentaires->removeElement($commentaire)) {
            // set the owning side to null (unless already changed)
            if ($commentaire->getUser() === $this) {
                $commentaire->setUser(null);
            }
        }

        return $this;
    }

    public function getFirstname(): ?string
    {
        return $this->firstname;
    }

    public function setFirstname(string $firstname= null): static
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname(): ?string
    {
        return $this->lastname;
    }

    public function setLastname(string $lastname= null): static
    {
        $this->lastname = $lastname;

        return $this;
    }

  


    /**
     * @return Collection<int, Consultation>
     */
    public function getConsultations(): Collection
    {
        return $this->consultations;
    }

    public function addConsultation(Consultation $consultation): static
    {
        if (!$this->consultations->contains($consultation)) {
            $this->consultations->add($consultation);
            $consultation->setPatient($this);
        }

        return $this;
    }

    public function removeConsultation(Consultation $consultation): static
    {
        if ($this->consultations->removeElement($consultation)) {
            // set the owning side to null (unless already changed)
            if ($consultation->getPatient() === $this) {
                $consultation->setPatient(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RendezVous>
     */
    public function getRendezVouses(): Collection
    {
        return $this->rendezVouses;
    }

    public function addRendezVouse(RendezVous $rendezVouse): static
    {
        if (!$this->rendezVouses->contains($rendezVouse)) {
            $this->rendezVouses->add($rendezVouse);
            $rendezVouse->setUser($this);
        }

        return $this;
    }

    public function removeRendezVouse(RendezVous $rendezVouse): static
    {
        if ($this->rendezVouses->removeElement($rendezVouse)) {
            // set the owning side to null (unless already changed)
            if ($rendezVouse->getUser() === $this) {
                $rendezVouse->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, RendezVous>
     */
    public function getRendezVousesP(): Collection
    {
        return $this->rendezVousesP;
    }

    public function addRendezVousesP(RendezVous $rendezVousesP): static
    {
        if (!$this->rendezVousesP->contains($rendezVousesP)) {
            $this->rendezVousesP->add($rendezVousesP);
            $rendezVousesP->setPatient($this);
        }

        return $this;
    }

    public function removeRendezVousesP(RendezVous $rendezVousesP): static
    {
        if ($this->rendezVousesP->removeElement($rendezVousesP)) {
            // set the owning side to null (unless already changed)
            if ($rendezVousesP->getPatient() === $this) {
                $rendezVousesP->setPatient(null);
            }
        }

        return $this;
    }
    #[ORM\Column(type: 'boolean')]
    private $isBanned = false;

    #[ORM\Column(type: 'datetime', nullable: true)]
    private $banExpiresAt;

    #[ORM\Column]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Like::class)]
    private Collection $likes;

    #[ORM\OneToMany(mappedBy: 'user', targetEntity: Formulaire::class)]
    private Collection $formulaires;

    public function getIsBanned(): bool
    {
        return $this->isBanned;
    }

    public function setIsBanned(bool $isBanned): self
    {
        $this->isBanned = $isBanned;
        return $this;
    }

    public function getBanExpiresAt(): ?DateTimeInterface
    {
        return $this->banExpiresAt;
    }

    public function setBanExpiresAt(?DateTimeInterface $banExpiresAt): self
    {
        $this->banExpiresAt = $banExpiresAt;
        return $this;
    }

    public function getCreatedAt(): ?\DateTimeImmutable
    {
        return $this->createdAt;
    }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }

    /**
     * @return Collection<int, Like>
     */
    public function getLikes(): Collection
    {
        return $this->likes;
    }

    public function addLike(Like $like): static
    {
        if (!$this->likes->contains($like)) {
            $this->likes->add($like);
            $like->setUser($this);
        }

        return $this;
    }

    public function removeLike(Like $like): static
    {
        if ($this->likes->removeElement($like)) {
            // set the owning side to null (unless already changed)
            if ($like->getUser() === $this) {
                $like->setUser(null);
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
            $formulaire->setUser($this);
        }

        return $this;
    }

    public function removeFormulaire(Formulaire $formulaire): static
    {
        if ($this->formulaires->removeElement($formulaire)) {
            // set the owning side to null (unless already changed)
            if ($formulaire->getUser() === $this) {
                $formulaire->setUser(null);
            }
        }

        return $this;
    }
     /**
     * @return Collection<int, Commande>
     */
    public function getCommandes(): Collection
    {
        return $this->commandes;
    }

    public function addCommande(Commande $commande): static
    {
        if (!$this->commandes->contains($commande)) {
            $this->commandes->add($commande);
            $commande->setUser($this);
        }

        return $this;
    }

    public function removeCommande(Commande $commande): static
    {
        if ($this->commandes->removeElement($commande)) {
            // set the owning side to null (unless already changed)
            if ($commande->getUser() === $this) {
                $commande->setUser(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Panier>
     */
    public function getPaniers(): Collection
    {
        return $this->paniers;
    }

    public function addPanier(Panier $panier): static
    {
        if (!$this->paniers->contains($panier)) {
            $this->paniers->add($panier);
            $panier->setUser($this);
        }

        return $this;
    }

    public function removePanier(Panier $panier): static
    {
        if ($this->paniers->removeElement($panier)) {
            // set the owning side to null (unless already changed)
            if ($panier->getUser() === $this) {
                $panier->setUser(null);
            }
        }

        return $this;
    }
}
