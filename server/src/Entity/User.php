<?php
namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Exception;


#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\InheritanceType('JOINED')]
#[ORM\DiscriminatorMap([
    "user" => User::class,
    "admin" => Admin::class
])]
/**
 * Represents a user account
 * A User account can manage {@link Wishlist}, invite users to contribute to them,
 * and directly contribute to them
 * 
 * @author Antonino Gillard <antonino.gillard@imt-atlantique.net>
 * @author Lucien Duhamel <lucien.duhamel@imt-atlantique.net>
 */
class User implements WishlistManagement
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $username = null;

    #[ORM\Column(length: 20)]
    private ?string $password = null;

    #[ORM\Column(length: 50)]
    private ?string $email = null;

    #[ORM\Column]
    private ?bool $isLocked = null;


    /**
     * @var Collection<int, Wishlist>
     */
    #[ORM\ManyToMany(targetEntity: Wishlist::class, inversedBy: 'contributors')]
    #[ORM\JoinTable(name: 'user_contributing_wishlists')]
    private Collection $contributingWishlists;

    /**
     * @var Collection<int, Wishlist>
     */
    #[ORM\ManyToMany(targetEntity: Wishlist::class, inversedBy: 'invitedUser')]
    #[ORM\JoinTable(name: 'user_invited_wishlists')]
    private Collection $invitedWishlists;

    /**
     * @var Collection<int, Wishlist>
     */
    #[ORM\ManyToOne(inversedBy: 'users')]
    #[ORM\JoinColumn(nullable: false)]
    private ?Website $website = null;

    /**
     * @var Collection<int, Wishlist>
     */
    #[ORM\OneToMany(targetEntity: Wishlist::class, mappedBy: 'author', orphanRemoval: true)]
    private Collection $wishlists;


    public function __construct()
    {
        $this->contributingWishlists = new ArrayCollection();
        $this->invitedWishlists = new ArrayCollection();
        $this->wishlists = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getUsername(): ?string
    {
        return $this->username;
    }

    public function setUsername(string $username): static
    {
        $this->username = $username;

        return $this;
    }

    public function getPassword(): ?string
    {
        return $this->password;
    }

    public function setPassword(string $password): static
    {
        $this->password = $password;

        return $this;
    }

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): static
    {
        $this->email = $email;

        return $this;
    }

    public function isLocked(): ?bool
    {
        return $this->isLocked;
    }

    public function setIsLocked(bool $isLocked): static
    {
        $this->isLocked = $isLocked;

        return $this;
    }

    /**
     * @return Collection<int, Wishlist>
     */
    public function getContributingWishlists(): Collection
    {
        return $this->contributingWishlists;
    }

    public function addContributingWishlist(Wishlist $contributingWishlist): static
    {
        if (!$this->contributingWishlists->contains($contributingWishlist)) {
            $this->contributingWishlists->add($contributingWishlist);
            $contributingWishlist->addContributor($this);
        }

        return $this;
    }

    public function removeContributingWishlist(Wishlist $contributingWishlist): static
    {
        if ($this->contributingWishlists->removeElement($contributingWishlist)) {
            $contributingWishlist->removeContributor($this);
        }

        return $this;
    }

    /**
     * @return Collection<int, Wishlist>
     */
    public function getInvitedWishlists(): Collection
    {
        return $this->invitedWishlists;
    }

    public function addInvitedWishlist(Wishlist $invitedWishlist): static
    {
        if (!$this->invitedWishlists->contains($invitedWishlist)) {
            $this->invitedWishlists->add($invitedWishlist);
            $invitedWishlist->addInvitedUser($this);
        }

        return $this;
    }

    public function removeInvitedWishlist(Wishlist $invitedWishlist): static
    {
        if ($this->invitedWishlists->removeElement($invitedWishlist)) {
            $invitedWishlist->removeInvitedUser($this);
        }

        return $this;
    }

    public function getWebsite(): ?Website
    {
        return $this->website;
    }

    public function setWebsite(?Website $website): static
    {
        $this->website = $website;

        return $this;
    }

    /**
     * Returns user's wishlist. Not invitation / contributing wishlists.
     * 
     * @return Collection<int, Wishlist>
     */
    public function getWishlists(): Collection
    {
        return $this->wishlists;
    }

    public function addWishlist(Wishlist $wishlist): static
    {
        if (!$this->wishlists->contains($wishlist)) {
            $this->wishlists->add($wishlist);
            $wishlist->setAuthor($this);
        }

        return $this;
    }


    public function removeWishlist(Wishlist $wishlist): static
    {
        if ($this->wishlists->removeElement($wishlist)) {
            // set the owning side to null (unless already changed)
            if ($wishlist->getAuthor() === $this) {
                $wishlist->setAuthor(null);
            }
        }

        return $this;
    }

    /**
     * Creates a {@link Wishlist}
     * 
     * @param string $name name of the wishlist
     * @param \DateTime $date deadline of the wishlist
     * @throws \Exception illegal parameters
     * @return Wishlist
     */
    public function createWishlist(string $name, \DateTime $date) : Wishlist {
        
        if (strlen($name) > 20) {
            throw new Exception("Name too long");
        }

        $isNameTaken = false;
        foreach ($this->wishlists as $wishlist) {
            if ($wishlist->getName() == $name) {
                $isNameTaken = true;
                break;
            }
        }

        if ($isNameTaken) {
            throw new Exception("Wishlist name already taken");
        }

        if (!UtilFilters::isValidDate($date)) {
            throw new Exception("Illegal date");
        }

        // Creates new Wishlist
        $wishlist = new Wishlist();
        $wishlist->setAuthor($this);
        $wishlist->setName($name);
        $wishlist->setDeadline($date);
        $this->wishlists->add($wishlist);

        return $wishlist;
    }

    /**
     * Allows the author of a {@link Wishlist} to edit its name / deadline.
     * 
     * @param \App\Entity\Wishlist $wishlist
     * @param string $name
     * @param \DateTime $date
     * @throws \Exception
     * @return Wishlist
     */
    public function editWishlist(Wishlist $wishlist, string $name, \DateTime $date) : Wishlist {
        
        if ($wishlist->getAuthor()->equals($this)) {
            throw new Exception("Only wishlist author can edit a wishlist");
        }

        if (strlen($name) > 20) {
            throw new Exception("Name too long");
        }

        if (!UtilFilters::isValidDate($date)) {
            throw new Exception("Illegal date");
        }
        

        $wishlist->setName($name);
        $wishlist->setDeadline($date);
        
        return $wishlist;
    }

    /**
     * Allows the author of a wishlist to delete it
     * 
     * @param \App\Entity\Wishlist $wishlist
     * @throws \Exception
     * @return void
     */
    public function deleteWishlist(Wishlist $wishlist) : void {

        if ($wishlist->getAuthor()->equals($this)) {
            throw new Exception("Only wishlist author can delete it");
        }

        $this->wishlists->removeElement($wishlist);

    }

    /**
     * Sends an invitation to contribute to a wishlist to a user of the service
     * 
     * @param string $username
     * @param \App\Entity\Wishlist $wishlist
     * @exception \Exception Author didn't send the invitation
     * @return void
     */
    public function sendInvitation(string $username, Wishlist $wishlist) : void {

        if (!$wishlist->getAuthor()->equals($this)) {
            throw new Exception("Only author can invite other users");
        }
        
        $user = $this->website->findUserByUsername($username);
        $user->addInvitedWishlist($wishlist);
        $wishlist->addInvitedUser($user);

    }

    /**
     * Accepts an inviation to contribute to a wishlist
     * 
     * @param \App\Entity\Wishlist $wishlist
     * @throws \Exception Not invited to the wishlist
     * @return void
     */
    public function acceptInvitation(Wishlist $wishlist) : void {
        
        if (!$this->invitedWishlists->contains($wishlist)) {
            throw new Exception("Invitation doesn't exist");
        }

        $wishlist->removeInvitedUser($this);
        $wishlist->addContributor($this);
        $this->removeInvitedWishlist($wishlist);
        $this->addContributingWishlist($wishlist);

    }

    /**
     * Refuses an inviation to contribute to a wishlist
     * 
     * @param \App\Entity\Wishlist $wishlist
     * @throws \Exception Not invited to the wishlist
     * @return void
     */
    public function refuseInvitation(Wishlist $wishlist) : void {

        if (!$this->invitedWishlists->contains($wishlist)) {
            throw new Exception("Invitation doesn't exist");
        }

        $wishlist->removeInvitedUser($this);
        $this->removeInvitedWishlist($wishlist);

    }

    public function equals(User $user):bool {
        return $this->username == $user->username;

    }


}
