<?php

namespace App\Entity;

#[ORM\Entity(repositoryClass: AdminRepository::class)]
/**
 * Represents an administrator account of the service.
 * Admin accounts can have access through implementations of the 
 * {@link AdminDashboard} and {@link AdminUserManagement} interfaces.
 * 
 * Such an implementation is the {@link Website} class.
 * 
 * @author Antonino Gillard <antonino.gillard@imt-atlantique.net>
 * @author Lucien Duhamel <lucien.duhamel@imt-atlantique.net>
 */
class Admin extends User {};
