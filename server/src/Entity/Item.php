<?php

namespace App\Entity;

use App\Repository\ItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: ItemRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorMap([
    "item" => Item::class,
    "purchasedItem" => PurchasedItem::class
])]
/**
 * Represents an Item, element of a {@link Wishlist}
 * Each item has a price, title, description.
 * 
 * @author Antonino Gillard <antonino.gillard@imt-atlantique.net>
 * @author Lucien Duhamel <lucien.duhamel@imt-atlantique.net>
 */
class Item
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    protected ?int $id = null;

    #[ORM\Column(length: 20)]
    protected ?string $title = null;

    #[ORM\Column(length: 500)]
    protected ?string $description = null;

    #[ORM\Column]
    protected ?float $price = null;

    #[ORM\Column(length: 200)]
    protected ?string $url = null;

    #[ORM\ManyToOne(inversedBy: 'items')]
    #[ORM\JoinColumn(nullable: false)]
    protected ?Wishlist $wishlish = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getTitle(): ?string
    {
        return $this->title;
    }

    public function setTitle(string $title): static
    {
        if (!$this->isValidTitle($title)) {
            throw new Exception("Title too long");
        }
        $this->title = $title;

        return $this;
    }

    public function getDescription(): ?string
    {
        return $this->description;
    }

    public function setDescription(string $description): static
    {
        if (!$this->isValidDescription($description)) {
            throw new Exception("Description too long");
        }
        $this->description = $description;

        return $this;
    }

    public function getPrice(): ?float
    {
        return $this->price;
    }

    public function setPrice(float $price): static
    {
        if ($price < 0) {
            throw new Exception("Cannot set negative price");
        }
        $this->price = $price;

        return $this;
    }

    public function getUrl(): ?string
    {
        return $this->url;
    }

    public function setUrl(string $url): static
    {
        if (!$this->isValidUrl($url)) {
            throw new Exception("Cannot set Invalid URL");
        }

        $this->url = $url;

        return $this;
    }

    public function getWishlist(): ?Wishlist
    {
        return $this->wishlish;
    }

    public function setWishlist(?Wishlist $newWishlist): static
    {
        $this->wishlish = $newWishlist;

        return $this;
    }

    protected function isValidTitle(string $title) {
        if (strlen($title) > 20) {
            return false;
        }
        return true;
    }

    protected function isValidUrl(string $url) {
        if (strlen($url) > 200) {
            return false;
        }
        return true;
    }

    protected function isValidDescription(string $desc) {
        if (strlen($desc) > 500) {
            return false;
        }
        return true;
    }

}
