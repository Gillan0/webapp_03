<?php

namespace App\Entity;

use App\Repository\WishlistRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\DBAL\Types\Types;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: WishlistRepository::class)]
class Wishlist implements WishlistContributor
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

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'contributingWishlists')]
    private Collection $contributors;

    /**
     * @var Collection<int, User>
     */
    #[ORM\ManyToMany(targetEntity: User::class, mappedBy: 'invitedWishlists')]
    private Collection $invitedUser;


    /**
     * @var Collection<int, Item>
     */
    #[ORM\OneToMany(targetEntity: Item::class, mappedBy: 'itemFromWishlist', orphanRemoval: true)]
    private Collection $items;

    #[ORM\ManyToOne(inversedBy: 'wishlists')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $author = null;

    public function __construct()
    {
        $this->contributors = new ArrayCollection();
        $this->invitedUser = new ArrayCollection();
        $this->items = new ArrayCollection();
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

    /**
     * @return Collection<int, Item>
     */
    public function getItems(SortOrder $sortOrder): Collection
    {
        return $this->items;
    }

    public function addItem(Item $item): static
    {
        if (!$this->items->contains($item)) {
            $this->items->add($item);
            $item->setItemFromWishlist($this);
        }

        return $this;
    }

    public function removeItem(Item $item): static
    {
        if ($this->items->removeElement($item)) {
            // set the owning side to null (unless already changed)
            if ($item->getItemFromWishlist() === $this) {
                $item->setItemFromWishlist(null);
            }
        }

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


    public function purchase(User $user, Item $item, string $proof) : PurchasedItem {

        $this->items->removeElement($item);

        // Build purchased item
        $purchased_item = new PurchasedItem();
        $purchased_item->setBuyer($user);
        $purchased_item->setPurchaseProof($proof);
        // Rebuild item part
        $purchased_item->setDescription($item->getDescription());
        $purchased_item->setTitle($item->getTitle());
        $purchased_item->setPrice($item->getPrice());
        $purchased_item->setUrl($item->getUrl());

        $this->items->add($purchased_item);

        return $purchased_item;
    }
}
