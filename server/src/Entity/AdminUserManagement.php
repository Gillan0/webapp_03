<?php

namespace App\Entity;

/**
 * Interface in charge of {@link User} management by {@link Admin} accounts.
 * An admin can lock / unlock and delete user accounts. 
 * 
 * @author Antonino Gillard <antonino.gillard@imt-atlantique.net>
 * @author Lucien Duhamel <lucien.duhamel@imt-atlantique.net> 
 */
interface AdminUserManagement 
{

    /**
     * Locks a user out of the service
     * 
     * @param \App\Entity\Admin $admin 
     * @param \App\Entity\User $user
     * @return void
     */
    public function lockUser(Admin $admin, User $user): void;

    /**
     * Unlocks a user
     * 
     * @param \App\Entity\Admin $admin 
     * @param \App\Entity\User $user
     * @return void
     */    
    public function unlockUser(Admin $admin, User $user): void;

    /**
     * Permanently deletes a user from the service
     * 
     * @param \App\Entity\Admin $admin
     * @param \App\Entity\User $user
     * @return void
     */
    public function deleteUser(Admin $admin, User $user): void;

    /**
     * Checks that username is valid (length)
     * @param string $user
     * @return void
     */
    public function isValidUsername(string $user): bool;

}

?>