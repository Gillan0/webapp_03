<?php

namespace App\Entity;
use Doctrine\Common\Collections\Collection;

interface WishlistManagement 
{

    public function createWishlist(string $name, \DateTime $date) : Wishlist;

    public function editWishlist(Wishlist $wishlist, string $name, \DateTime $date) : Wishlist;

    public function deleteWishlist(Wishlist $wishlist) : void;

    public function sendInvitation(string $username, Wishlist $wishlist) : void;

    public function acceptInvitation(Wishlist $wishlist) : void;

    public function refuseInvitation(Wishlist $wishlist) : void;

    public function getWishlists() : Collection;

}

?>