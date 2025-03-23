<?php

namespace App\Entity;

use App\Repository\WebsiteRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;

#[ORM\Entity(repositoryClass: WebsiteRepository::class)]
/**
 * Main class of the namespace. 
 * Represents the service and directly contains all {@link User} accounts ({@link Admin} included).
 * 
 * @author Antonino Gillard <antonino.gillard@imt-atlantique.net>
 * @author Lucien Duhamel <lucien.duhamel@imt-atlantique.net>
 */
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
     * Gets the top 5 most expensive items in a wishlist
     * 
     * @param \App\Entity\Wishlist $wishlist
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMostExpensiveItems(Wishlist $wishlist) : Collection {
        $items =$wishlist->getItems(SortOrder::PriceDescending);

        return $items->slice(0,5);
    }

    /**
     * Gets the top 5 most expensive wishlists in the service
     * 
     * @param \App\Entity\Wishlist $wishlist
     * @return Doctrine\Common\Collections\Collection
     */
    public function getMostExpensiveWishlists() : Collection {
        Criteria::create()->orderBy(['totalPrice' => Criteria::DESC]);
        
        // Fetches all wishlists across the service
        $wishlists = new Collection();
        foreach ($this->users as $user) {
            foreach ($user->getWishlist() as $wishlist) {
                $wishlists->add($wishlist);
            }
        } 
        
        // Assigns a price to the wishlists
        $wishlistsWithPrice = $wishlists->map(fn($wishlist) => [
            'wishlist' => $wishlist,
            'totalPrice' => $wishlist->getTotalPrice()
        ])->toArray();
    
        // Sorts them by price and returns the top 5 most expensive
        usort($wishlistsWithPrice, fn($a, $b) => $b['totalPrice'] <=> $a['totalPrice']);
        $topWishlists = array_slice($wishlistsWithPrice, 0, 5);
        return new Collection(array_map(fn($data) => $data['wishlist'], $topWishlists));
    }

    /**
     * Locks a user out of the service
     * 
     * @param \App\Entity\Admin $admin
     * @param \App\Entity\User $user
     * @throws \Exception user or admin account not recognized.
     * @return void
     */
    public function lockUser(Admin $admin, User $user): void {

        if (!$this->users->contains($admin) || !$this->users->contains($user)) {
            throw new Exception("User account or Admin account not in table");
        }

        $user->setIsLocked(true);
    }

    /**
     * Unlocks a {@link User} account
     *
     * @param \App\Entity\Admin $admin
     * @param \App\Entity\User $user
     * @throws \Exception user or admin account not recognized
     * @return void
     */
    public function unlockUser(Admin $admin, User $user): void {
        if (!$this->users->contains($admin) || !$this->users->contains($user)) {
            throw new Exception("User account or Admin account not in table");
        }

        $user->setIsLocked(false);
    }

    /**
     * Permanently deletes a user from the service
     * 
     * @param \App\Entity\Admin $admin
     * @param \App\Entity\User $user
     * @throws \Exception user or admin account not recognized
     * @return void
     */
    public function deleteUser(Admin $admin, User $user): void {
        if (!$this->users->contains($admin) || !$this->users->contains($user)) {
            throw new Exception("User account or Admin account not in table");
        }

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
     * Creates a new user and returns the newly created {@link User} account
     * 
     * @param string $username
     * @param string $password
     * @param string $email
     * @throws \Exception invalid parameters
     * @return User Newly created account
     */
    public function createUser(string $username,
                                string $password,
                                string $email): User  {
        if (!$this->isValidCredentials($username, $password, $email)) {
            throw new Exception("Invalid account parameters");
        }

        // Check is username if not already used
        $isUsernameAvailable = true;
        foreach ($this->users as $user) {
            if ($user->getUsername() == $username) {
                $isUsernameAvailable = false;
                break;
            }
        }

        if (!$isUsernameAvailable) {
            throw new Exception("Username already used");
        }

        // Building Object
        $user = new User();
        $user->setUsername($username);
        $user->setPassword(password_hash($password, PASSWORD_DEFAULT));
        $user->setEmail($email);
        $user->setIsLocked(false);
        $user->setWebsite($this);

        $this->users->add($user);
        return $user;
    }

    /**
     * Handles login and returns the {@link User} account if credentials are correct
     * 
     * @param string $username
     * @param string $password Unhashed password
     * @throws \Exception
     * @return \App\Entity\User
     */
    public function login(string $username, string $password) : User {
        if (strlen($username) > 20 || strlen($password) > 255) {
            throw new Exception("Illegal arguments");
        }
        // Searches user
        $correspondingUser = null;
        foreach ($this->users as $user) {
            if ($username == $user->getUsername() && password_verify($password, $user->getPassword())) {
                $correspondingUser = $user;
                break;
            }
        }

        // Check if user found
        if (empty($correspondingUser)) {
            throw new Exception("Wrong credentials");
        }

        return $correspondingUser;
    }

    /**
     * Checks that username and password are valid (ie length, type of characters)
     * 
     * @param string $username
     * @param string $password
     * @param string $email
     * @return bool if parameters are valid 
     */
    public function isValidCredentials(string $username,
                                        string $password,
                                        string $email) : bool {
        if (strlen($username) >20) {
            return false;
        }
        if (strlen($password) >20) {
            return false;
        }
        if (strlen($email) >50) {
            return false;
        }
        // Is an email address
        if (!preg_match("/^([a-zA-Z0-9._%-]+@[a-zA-Z0-9.-]+\.[a-zA-Z]{2,})$/ ", $email)) {
            return false;

        }
        return true;                                   
    }

    /**
     * Returns a user if username matches
     * 
     * @param string $username
     * @throws \Exception
     * @return \App\Entity\User
     */
    public function findUserByUsername(string $username) : User {
        $correspondingUser = null;
        foreach ($this->users as $user) {
            if ($username == $user->getUsername()) {
                $correspondingUser = $user;
                break;
            }
        }

        // Check if user found
        if (empty($correspondingUser)) {
            throw new Exception("No user with such username");
        }

        return $correspondingUser;
    }

}
