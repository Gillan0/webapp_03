<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Repository\WishlistRepository;
use App\Repository\ItemRepository;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Doctrine\ORM\EntityManagerInterface;

/**
 * @author Suzanne Veignant <suzanne.veignant@imt-atlantique.net>
 * @author Julien Abraul Guilherme "<julien.abraul-guilherme@imt-atlantique.net>
**/

final class PurchaseProofController extends AbstractController
{
    private WishlistRepository $wishlistRepository;
    private ItemRepository $itemRepository;
    private EntityManagerInterface $entityManager;

    public function __construct(WishlistRepository $wishlistRepository, ItemRepository $itemRepository, EntityManagerInterface $entityManager )
    {
        $this->wishlistRepository = $wishlistRepository;
        $this->itemRepository = $itemRepository;
        $this->entityManager = $entityManager;
    }

    #[Route('/purchase/proof/{display_url}/{item_id}', name: 'app_purchase_proof', methods: ['GET', 'POST'])]
    public function upload(string $display_url, string $item_id, Request $request): Response
    {
        $buyerName = $request->request->get('buyer_name', '');
        $congratuloryMessage = $request->request->get('congratulory_message', '');
        $file = $request->files->get('file');

        $successMessage = null;
        $errorMessage = null;
        $fileName = null;

        // Vérifier si la wishlist et l'item existent
        $wishlist = $this->wishlistRepository->findOneBy(['displayUrl' => $display_url]);
        $item = $this->itemRepository->findOneBy(['id' => $item_id]);

        if (!$wishlist) {
            return new Response('Erreur : Wishlist introuvable.', Response::HTTP_NOT_FOUND);
        }

        if (!$item) {
            return new Response('Erreur : Item introuvable.', Response::HTTP_NOT_FOUND);
        }


        if ($request->isMethod('POST')) {
            if ($file instanceof UploadedFile) {
                $allowedExtensions = ['jpg', 'jpeg', 'png', 'pdf'];
                $extension = $file->guessExtension();

                if (!in_array($extension, $allowedExtensions)) {
                    $errorMessage = 'Format de fichier non autorisé. Seuls JPG, PNG et PDF sont acceptés.';
                } else {
                    $uploadsDirectory = $this->getParameter('uploads_directory');

                    if (!$uploadsDirectory) {
                        throw new \Exception('Le paramètre "uploads_directory" n\'est pas défini.');
                    }

                    $fileName = uniqid() . '.' . $extension;

                    try {
                        $file->move($uploadsDirectory, $fileName);
                        $successMessage = 'Message et fichier envoyés avec succès !';

                        // Appel de la méthode purchase
                        $purchasedItem = $wishlist->purchase($buyerName, $item, $fileName);

                        // Sauvegarde des modifications en base de données
                        $this->entityManager->persist($purchasedItem);
                        $this->entityManager->flush();

                        // Redirection après succès
                        return $this->redirectToRoute('app_sharing_wishlist', [
                            'display_url' => $display_url
                        ]);
                    } catch (\Exception $e) {
                        $errorMessage = 'Erreur lors de l\'upload du fichier : ' . $e->getMessage();
                    }
                }
            } else {
                $errorMessage = 'Veuillez sélectionner un fichier à envoyer.';
            }
        }

        return $this->render('purchase_proof/purchase_proof.html.twig', [
            'success' => $successMessage,
            'error' => $errorMessage,
            'buyer_name' => $buyerName,
            'congratulory_message' => $congratuloryMessage,
            'display_url' => $display_url,
            'item_id' => $item_id
        ]);
    }
}
