<?php

namespace App\Entity;
use Doctrine\Common\Collections\Collection;

interface AdminDashboard 
{

    /**
     * Summary of getMostExpensiveItems
     * @param \App\Entity\Wishlist $wishlist
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMostExpensiveItems(Wishlist $wishlist) : Collection;

    /**
     * Summary of getMostExpensiveWishlists
     * @param \App\Entity\Wishlist $wishlist
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMostExpensiveWishlists(Wishlist $wishlist) : Collection;
}

?>