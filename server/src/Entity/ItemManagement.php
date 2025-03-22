<?php

namespace App\Entity;

/**
 * Interface in charge of managing a wishlist's items.
 * 
 * This includes adding, editing and removing items from the wishlist.
 * 
 * @author Antonino Gillard <antonino.gillard@imt-atlantique.net>
 * @author Lucien Duhamel <lucien.duhamel@imt-atlantique.net> 
 */
interface ItemManagement 
{

    /**
     * Adds an {@link Item} to the Wishlist
     * 
     * @param string $title
     * @param string $description
     * @param string $url
     * @return void
     */
    public function addItemParams(string $title, string $description, string $url, float $price) : Item;

    /**
     * Edits an {@link Item} of the Wishlist
     * 
     * @param \App\Entity\Item $item
     * @param string $title
     * @param string $description
     * @param string $url
     * @return void
     */
    public function editItemParams(Item $item, string $title, string $description, string $url, float $price) : Item;

    /**
     * Removes an {@link Item} of the Wishlist
     * 
     * @param \App\Entity\Item $item
     * @param string $title
     * @param string $description
     * @param string $url
     * @return void
     */
    public function removeItemParams(Item $item) : void;

}

?>