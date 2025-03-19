<?php

namespace App\Entity;

use App\Repository\WishlistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WishlistRepository::class)]
class Wishlist
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(type: Types::DATE_MUTABLE)]
    private ?\DateTimeInterface $deadline = null;

    #[ORM\Column(length: 20)]
    private ?string $name = null;

    #[ORM\Column(length: 100)]
    private ?string $sharingUrl = null;

    #[ORM\Column(length: 100)]
    private ?string $displayUrl = null;

    #[ORM\ManyToOne(inversedBy: 'wishlists')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, inversedBy: 'contributingWishlists')]
    private Collection $contributors;

    /**
     * @var Collection<int, Item>
     */
    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'wishlist')]
    private Collection $items;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class)]
    private Collection $invitedUser;

    public function __construct()
    {
        $this->contributors = new ArrayCollection();
        $this->items = new ArrayCollection();
        $this->invitedUser = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getDeadline(): ?\DateTimeInterface
    {
        return $this->deadline;
    }

    public function setDeadline(\DateTimeInterface $deadline): static
    {
        $this->deadline = $deadline;

        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): static
    {
        $this->name = $name;

        return $this;
    }

    public function getSharingUrl(): ?string
    {
        return $this->sharingUrl;
    }

    public function setSharingUrl(string $sharingUrl): static
    {
        $this->sharingUrl = $sharingUrl;

        return $this;
    }

    public function getDisplayUrl(): ?string
    {
        return $this->displayUrl;
    }

    public function setDisplayUrl(string $displayUrl): static
    {
        $this->displayUrl = $displayUrl;

        return $this;
    }

    public function getAuthor(): ?User
    {
        return $this->author;
    }

    public function setAuthor(?User $author): static
    {
        $this->author = $author;

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getContributors(): Collection
    {
        return $this->contributors;
    }

    public function addContributor(User $contributor): static
    {
        if (!$this->contributors->contains($contributor)) {
            $this->contributors->add($contributor);
        }

        return $this;
    }

    public function removeContributor(User $contributor): static
    {
        $this->contributors->removeElement($contributor);

        return $this;
    }

    /**
     * @return Collection<int, Item>
     */
    public function getItems(): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setWishlist($this);
        }

        return $this;
    }

    public function removeItem(Item $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getWishlist() === $this) {
                $item->setWishlist(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, User>
     */
    public function getInvitedUser(): Collection
    {
        return $this->invitedUser;
    }

    public function addInvitedUser(User $invitedUser): static
    {
        if (!$this->invitedUser->contains($invitedUser)) {
            $this->invitedUser->add($invitedUser);
        }

        return $this;
    }

    public function removeInvitedUser(User $invitedUser): static
    {
        $this->invitedUser->removeElement($invitedUser);

        return $this;
    }
}
