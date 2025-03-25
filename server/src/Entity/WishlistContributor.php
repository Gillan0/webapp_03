<?php

namespace App\Entity;
use Doctrine\Common\Collections\Collection;

/**
 * Interface in charge of actions a {@link Wishlist} contributor can perform 
 * Such actions are getting information about the wishlist and purchasing
 * an {@link Item}
 * 
 * @author Antonino Gillard <antonino.gillard@imt-atlantique.net>
 * @author Lucien Duhamel <lucien.duhamel@imt-atlantique.net> 
 */
interface WishlistContributor 
{

    /**
     * Returns name of the wishlist
     * 
     * @return ?string
     */
    public function getName() : ?string;

    /**
     * Returns author account of the wishlist
     * @return ?User
     */
    public function getAuthor() : ?User;

    /**
     * Returns all items of the wishlist sorted by a {@link SortOrder}
     * @param \App\Entity\SortOrder $order
     * @return void
     */
    public function getSortedItems(SortOrder $order) : Collection;

    /**
     * Purchases an {@link Item} from the wishlist
     * Removes the item from the wishlist and replaces it with a {@link PurchasedItem}
     * in the Item collection
     * 
     * @param string $username alleged name of the person who buys the item
     * @param \App\Entity\Item $item
     * @param string $proof
     * @return PurchasedItem
     */
    public function purchase(string $username, Item $item, string $proof) : PurchasedItem;

}

?>