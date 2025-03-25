<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Repository\WishlistRepository;
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
    #[Route('{display_url}', name: 'app_sharing_wishlist')]
    public function show(string $display_url): Response
    
    {
        $wishlist = $this->wishlistRepository->findOneBy(['displayUrl' => $display_url]);

        if (!$wishlist) {
            throw $this->createNotFoundException('Wishlist not found');
        }

        return $this->render('sharing_wishlist/displayUrl.html.twig', [
            'wishlist' => $wishlist
        ]);
    }
}
