<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\WishlistRepository;

/**
 * @author Suzanne Veignant <suzanne.veignant@imt-atlantique.net>
 * @author Julien Abraul Guilherme "<julien.abraul-guilherme@imt-atlantique.net>
**/

final class PurchaseProofController extends AbstractController
{
    private WishlistRepository $wishlistRepository;

    public function __construct(WishlistRepository $wishlistRepository)
    {
        $this->wishlistRepository = $wishlistRepository;
    }

    #[Route('/purchase/proof/{display_url}/{item_id}', name: 'app_purchase_proof', methods: ['GET', 'POST'])]
    public function upload(int $item_id, string $display_url, Request $request): Response
    {
        $message1 = $request->request->get('buyer_name');
        $message2 = $request->request->get('congratulory_message');
        $file = $request->files->get('file');

        $successMessage = null;
        $errorMessage = null;

        if ($request->isMethod('POST')) {
            if ($file) {
                $uploadsDirectory = $this->getParameter('uploads_directory');

                if (!$uploadsDirectory) {
                    throw new \Exception('Le paramètre "uploads_directory" n\'est pas défini.');
                }

                $fileName = uniqid() . '.' . $file->guessExtension();

                try {
                    $file->move($uploadsDirectory, $fileName);
                    $successMessage = 'Message et fichier envoyés avec succès !';
                } catch (\Exception $e) {
                    $errorMessage = 'Erreur lors de l\'upload du fichier : ' . $e->getMessage();
                }
            } else {
                $errorMessage = 'Veuillez sélectionner un fichier à envoyer.';
            }
        }

        if ($successMessage) {
            //faire en sorte que l'item passe en purchased item
            //faire passer l'item comme acheté
            // Rediriger vers la page de la wishlist après la soumission réussie
            return $this->redirectToRoute('app_sharing_wishlist', [
                    'display_url' => $display_url,
                    //'item_id' => $item_id
                ]);
        }

        return $this->render('purchase_proof/purchase_proof.html.twig', [
            'success' => $successMessage,
            'error' => $errorMessage,
            'buyer_name' => $message1,
            'congratulory_message' => $message2,
            'display_url' => $display_url,
            'item_id' => $item_id

        ]);
    }
}
