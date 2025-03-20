<?php

namespace App\Entity;
use Doctrine\Common\Collections\Collection;

interface WishlistContributor 
{

    public function getName() : ?string;

    public function getAuthor() : ?User;

    public function getItems(SortOrder $order) : Collection;

    public function purchase(User $user, Item $item, string $proof) : PurchasedItem;

}

?>