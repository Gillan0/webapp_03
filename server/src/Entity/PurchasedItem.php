<?php

namespace App\Entity;

use App\Repository\PurchasedItemRepository;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: PurchasedItemRepository::class)]
/**
 * Represents an {@link Item} which has been purchased.
 * A congratulary message and a purchase proof are added to the former Item.
 * 
 * @author Antonino Gillard <antonino.gillard@imt-atlantique.net>
 * @author Lucien Duhamel <lucien.duhamel@imt-atlantique.net>
 */
class PurchasedItem extends Item
{

    #[ORM\Column(length: 500, nullable : true)]
    private ?string $congratuloryMessage = null;

    #[ORM\Column(length: 200)]
    private ?string $purchaseProof = null;

    #[ORM\Column(length:20)]
    private ?string $buyer = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getCongratuloryMessage(): ?string
    {
        return $this->congratuloryMessage;
    }

    public function setCongratuloryMessage(?string $congratuloryMessage): static
    {
        if (!$this->isValidMessage($congratuloryMessage)) {
            throw new Exception("Congratulatory Message too long");
        }
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

    public function getBuyer(): ?string
    {
        return $this->buyer;
    }

    public function setBuyer(?string $buyer): static
    {
        $this->buyer = $buyer;

        return $this;
    }

    private function isValidMessage(string $message) : bool {
        if (strlen($message) > 500) {
            return false;
        }
        return true;
    }

}