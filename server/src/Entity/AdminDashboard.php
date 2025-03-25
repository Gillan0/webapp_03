<?php

namespace App\Entity;
use Doctrine\Common\Collections\Collection;

/**
 * Interface which contains methods relative to information about the service.
 * e.g : we can get the most expensive items and most expensive wishlists
 * 
 * @author Antonino Gillard <antonino.gillard@imt-atlantique.net>
 * @author Lucien Duhamel <lucien.duhamel@imt-atlantique.net> 
 */
interface AdminDashboard 
{

    /**
     * Gets the top 5 most expensive items in a wishlist
     * 
     * @param \App\Entity\Wishlist $wishlist
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMostExpensiveItems(Wishlist $wishlist) : Collection;

    /**
     * Gets the top 5 most expensive wishlists in the service
     * 
     * @param \App\Entity\Wishlist $wishlist
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMostExpensiveWishlists() : Collection;
}

?>