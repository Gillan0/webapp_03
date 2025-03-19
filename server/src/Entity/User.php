<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: UserRepository::class)]
class User
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $username = null;

    #[ORM\Column(length: 20)]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $email = null;

    #[ORM\Column]
    private ?bool $isLocked = null;

    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Website $website = null;

    /**
     * @var Collection<int, Wishlist>
     */
    #[ORM\OneToMany(targetEntity: Wishlist::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $wishlists;

    /**
     * @var Collection<int, Wishlist>
     */
    #[ORM\ManyToMany(targetEntity: Wishlist::class, mappedBy: 'contributors')]
    private Collection $contributingWishlists;

    public function __construct()
    {
        $this->wishlists = new ArrayCollection();
        $this->contributingWishlists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
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

    public function isLocked(): ?bool
    {
        return $this->isLocked;
    }

    public function setIsLocked(bool $isLocked): static
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    public function getWebsite(): ?Website
    {
        return $this->website;
    }

    public function setWebsite(?Website $website): static
    {
        $this->website = $website;

        return $this;
    }

    /**
     * @return Collection<int, Wishlist>
     */
    public function getWishlists(): Collection
    {
        return $this->wishlists;
    }

    public function addWishlist(Wishlist $wishlist): static
    {
        if (!$this->wishlists->contains($wishlist)) {
            $this->wishlists->add($wishlist);
            $wishlist->setAuthor($this);
        }

        return $this;
    }

    public function removeWishlist(Wishlist $wishlist): static
    {
        if ($this->wishlists->removeElement($wishlist)) {
            // set the owning side to null (unless already changed)
            if ($wishlist->getAuthor() === $this) {
                $wishlist->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Wishlist>
     */
    public function getContributingWishlists(): Collection
    {
        return $this->contributingWishlists;
    }

    public function addContributingWishlist(Wishlist $contributingWishlist): static
    {
        if (!$this->contributingWishlists->contains($contributingWishlist)) {
            $this->contributingWishlists->add($contributingWishlist);
            $contributingWishlist->addContributor($this);
        }

        return $this;
    }

    public function removeContributingWishlist(Wishlist $contributingWishlist): static
    {
        if ($this->contributingWishlists->removeElement($contributingWishlist)) {
            $contributingWishlist->removeContributor($this);
        }

        return $this;
    }
}
