<?php

namespace App\Entity;
use Doctrine\Common\Collections\Collection;

/**
 * Interface in charge of managing a {@link User}'s {@link Wishlist}.
 * Wishlists can be created, edited and deleted by their author.
 * The author can also invite users to contribute to the wishlist.
 * 
 * All users can accept / refuse invitations to wishlists.
 * 
 * @author Antonino Gillard <antonino.gillard@imt-atlantique.net>
 * @author Lucien Duhamel <lucien.duhamel@imt-atlantique.net>
 */
interface WishlistManagement 
{
    /**
     * Creates a {@link Wishlist}
     * 
     * @param string $name name of the wishlist
     * @param \DateTime $date deadline of the wishlist
     * @throws \Exception illegal parameters
     * @return Wishlist
     */
    public function createWishlist(string $name, \DateTime $date) : Wishlist;

    /**
     * Allows the author of a {@link Wishlist} to edit its name / deadline.
     * 
     * @param \App\Entity\Wishlist $wishlist
     * @param string $name
     * @param \DateTime $date
     * @throws \Exception
     * @return Wishlist
     */
    public function editWishlist(Wishlist $wishlist, string $name, \DateTime $date) : Wishlist;

    /**
     * Allows the author of a wishlist to delete it
     * 
     * @param \App\Entity\Wishlist $wishlist
     * @throws \Exception
     * @return void
     */
    public function deleteWishlist(Wishlist $wishlist) : void;

     /**
     * Sends an invitation to contribute to a wishlist to a user of the service
     * 
     * @param string $username
     * @param \App\Entity\Wishlist $wishlist
     * @exception \Exception Author didn't send the invitation
     * @return void
     */
    public function sendInvitation(string $username, Wishlist $wishlist) : void;

     /**
     * Accepts an inviation to contribute to a wishlist
     * 
     * @param \App\Entity\Wishlist $wishlist
     * @throws \Exception Not invited to the wishlist
     * @return void
     */
    public function acceptInvitation(Wishlist $wishlist) : void;

    /**
     * Refuses an inviation to contribute to a wishlist
     * 
     * @param \App\Entity\Wishlist $wishlist
     * @throws \Exception Not invited to the wishlist
     * @return void
     */
    public function refuseInvitation(Wishlist $wishlist) : void;

    /**
     * Returns user's wishlist. Not invitation / contributing wishlists.
     * 
     * @return Collection<int, Wishlist>
     */
    public function getWishlists() : Collection;

}

?>