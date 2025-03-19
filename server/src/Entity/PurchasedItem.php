<?php

namespace App\Entity;

use App\Repository\PurchasedItemRepository;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: PurchasedItemRepository::class)]
class PurchasedItem
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 500)]
    private ?string $congratuloryMessage = null;

    #[ORM\Column(length: 200)]
    private ?string $purchaseProof = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $buyer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCongratuloryMessage(): ?string
    {
        return $this->congratuloryMessage;
    }

    public function setCongratuloryMessage(string $congratuloryMessage): static
    {
        $this->congratuloryMessage = $congratuloryMessage;

        return $this;
    }

    public function getPurchaseProof(): ?string
    {
        return $this->purchaseProof;
    }

    public function setPurchaseProof(string $purchaseProof): static
    {
        $this->purchaseProof = $purchaseProof;

        return $this;
    }

    public function getBuyer(): ?User
    {
        return $this->buyer;
    }

    public function setBuyer(?User $buyer): static
    {
        $this->buyer = $buyer;

        return $this;
    }
}
