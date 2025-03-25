<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\WishlistRepository;

use App\Entity\SortOrder;

/**
 * @author Suzanne Veignant <suzanne.veignant@imt-atlantique.net>
 * @author Julien Abraul Guilherme "<julien.abraul-guilherme@imt-atlantique.net>
**/
final class SharingWishlistController extends AbstractController
{
    private WishlistRepository $wishlistRepository;

    public function __construct(WishlistRepository $wishlistRepository)
    {
        $this->wishlistRepository = $wishlistRepository;
    }
    #[Route('{display_url}/{sorted?}', name: 'app_sharing_wishlist', requirements: ["sorted" => "asc|desc"])]
    public function show(string $display_url,
                        ?string $sorted): Response
    
    {
        if (empty($sorted)) {
            $sorted = 'asc';
        }

        $wishlist = $this->wishlistRepository->findOneBy(['displayUrl' => $display_url]);

        if (!$wishlist) {
            throw $this->createNotFoundException('Wishlist not found');
        }

        $sortOrder = ($sorted == "asc") ? SortOrder::PriceAscending : SortOrder::PriceDescending;
        $itemSorted = $wishlist->getSortedItems($sortOrder);

        return $this->render('sharing_wishlist/displayUrl.html.twig', [
            'wishlist' => $wishlist,
            'sortedItems' => $itemSorted,
            "otherOrder" => ($sorted=="asc") ? "desc" : "asc"
        ]);
    }
}
