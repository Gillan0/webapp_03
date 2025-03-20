<?php

namespace App\Entity;

use App\Repository\WebsiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: WebsiteRepository::class)]
class Website implements Login, AdminUserManagement, AdminDashboard
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    /**
     * @var Collection<int, User>
     */
    #[ORM\OneToMany(targetEntity: User::class, mappedBy: 'website', orphanRemoval: true)]
    private Collection $users;

    public function __construct()
    {
        $this->users = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    /**
     * @return Collection<int, User>
     */
    public function getUsers(): Collection
    {
        return $this->users;
    }

    public function addUser(User $user): static
    {
        if (!$this->users->contains($user)) {
            $this->users->add($user);
            $user->setWebsite($this);
        }

        return $this;
    }

    public function removeUser(User $user): static
    {
        if ($this->users->removeElement($user)) {
            // set the owning side to null (unless already changed)
            if ($user->getWebsite() === $this) {
                $user->setWebsite(null);
            }
        }

        return $this;
    }


     /**
     * Summary of getMostExpensiveItems
     * @param \App\Entity\Wishlist $wishlist
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMostExpensiveItems(Wishlist $wishlist) : Collection {
        // TO DO //
        return new Collection();
    }

    /**
     * Summary of getMostExpensiveWishlists
     * @param \App\Entity\Wishlist $wishlist
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMostExpensiveWishlists(Wishlist $wishlist) : Collection {
        // TO DO //
        return new Collection();
    }

    /**
     * Locks a user out of the service
     * 
     * @param \App\Entity\Admin $admin 
     * @param \App\Entity\User $user
     * @return void
     */
    public function lockUser(Admin $admin, User $user): void {
        // TO DO

        $user->setIsLocked(true);
    }

    /**
     * Unlocks a user
     * 
     * @param \App\Entity\Admin $admin 
     * @param \App\Entity\User $user
     * @return void
     */    
    public function unlockUser(Admin $admin, User $user): void {
        // TO DO //

        $user->setIsLocked(false);
    }

    /**
     * Permanently deletes a user from the service
     * 
     * @param \App\Entity\Admin $admin
     * @param \App\Entity\User $user
     * @return void
     */
    public function deleteUser(Admin $admin, User $user): void {
        // TO DO //
        

        $this->users->removeElement($user);
    }

    /**
     * Checks that username is valid (length)
     * @param string $user
     * @return void
     */
    public function isValidUsername(string $user): bool {
        if (strlen($user) > 20) {
            return false;
        }
        return true;
    }

    /**
     * Creates a new user
     * 
     * @param string $username
     * @param string $password
     * @param string $email
     * @return User 
     */
    public function createUser(string $username,
                                string $password,
                                string $email): User  {
        if (!$this->isValidCredentials($username, $password, $email)) {
            throw new Exception("Invalid account parameters");
        }

        // Building Object
        $user = new User();
        $user->setUsername($username);
        $user->setPassword($password);
        $user->setEmail($email);

        $this->users->add($user);
        return $user;
    }

    /**
     * Handles login
     * 
     * @param string $username
     * @param string $password
     * @return User
     */
    public function login(string $username, string $password) : User {
        if (!$this->isValidCredentials($username, $password, null)) {
            throw new Exception("Illegal arguments");
        }

        $user = $this->users->get();
        return $user;
    }

    /**
     * Checks that username and password are valid (ie length, type of characters)
     */
    public function isValidCredentials(string $username,
                                        string $password,
                                        string $email) : bool {
        // TO DO //
        return true;                                   
    }

}
