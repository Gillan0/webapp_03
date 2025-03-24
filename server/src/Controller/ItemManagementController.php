<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wishlist;
use App\Entity\Item;
use App\Repository\UserRepository;
use App\Repository\WishlistRepository;
use App\Repository\ItemRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * All routes needed for ItemManagement Page (Case 1)
 * 
 * This includes the main page, but also adding, editing and deleting items 
 * from a wishlist.
 * 
 * @author Antonino Gillard <antonino.gillard@imt-atlantique.net>
 * @author Lucien Duhamel <lucien.duhamel@imt-atlantique.net> 
 */
final class ItemManagementController extends AbstractController
{

    #[Route('/{username}/itemManagement/{wishlist_name}', name: 'app_user_item_management', methods: ['GET','POST'])]
    public function manageWishlist(Request $request, 
                        string $username, 
                        string $wishlist_name, 
                        UserRepository $userRepository, 
                        WishlistRepository $wishlistRepository,
                        EntityManagerInterface $entityManager): Response
    {
        try {
            $user = ConnexionController::handleSession($request, $username,  $userRepository);
        } catch (Exception $e) {
            return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
        }
        
        $wishlist = $wishlistRepository->findOneBy(['name' => $wishlist_name]);

        if (empty($wishlist)) {
            return $this->redirectToRoute('app_list_wishlists', ['username' => $user->getUsername()], Response::HTTP_SEE_OTHER);
        }

        if (!$wishlist->getAuthor()->equals($user)) {
            return $this->redirectToRoute('app_list_wishlists', ['username' => $user->getUsername()], Response::HTTP_SEE_OTHER);
        }

        $form = $this->createFormBuilder(new Item())
        ->add('title', TextType::class)
        ->add('description', TextType::class)
        ->add('url', UrlType::class)
        ->add('price', NumberType::class, ['html5' => true,])
        ->getForm(); 

        $errorMessage = null;

        try {            
            $form->handleRequest($request);
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $title = trim(htmlspecialchars($formData->getTitle()));
            $description = trim(htmlspecialchars($formData->getDescription()));
            $url = trim(htmlspecialchars($formData->getUrl()));
            $price = trim(htmlspecialchars($formData->getPrice()));

            try {
                $item = $wishlist->addItemParams($title, $description, $url, $price);
                
                $entityManager->persist($wishlist);
                $entityManager->persist($item);

                $entityManager->flush();

                return $this->redirectToRoute('app_user_item_management',
                                             ["username" => $user->getUsername(),
                                            "wishlist_name" => $wishlist->getName()], 
                                             Response::HTTP_SEE_OTHER);
            } catch (Exception $e) {
                $errorMessage = $e->getMessage(); 
            }    
        }

        return $this->render('case1/item_management.html.twig', [
            'title' => $wishlist->getName(),
            'user' => $user,
            'wishlist' => $wishlist,
            'form' => $form,
            'error' => $errorMessage,
        ]);
    }

    #[Route('/{username}/itemManagement/{wishlist_id}/delete/{item_id}', name: 'app_user_delete_item', methods: ['GET','POST'])]
    public function deleteItem(Request $request, 
                        string $username, 
                        string $wishlist_id, 
                        string $item_id,
                        UserRepository $userRepository, 
                        WishlistRepository $wishlistRepository,
                        ItemRepository $itemRepository,
                        EntityManagerInterface $entityManager): Response
    {
        try {
            $user = ConnexionController::handleSession($request, $username,  $userRepository);
        } catch (Exception $e) {
            return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
        }

        $wishlist = $wishlistRepository->findOneBy(['id' => $wishlist_id]);
        if (empty($wishlist)) {
            return $this->redirectToRoute('app_list_wishlists', [], Response::HTTP_SEE_OTHER);
        }
        
        if (!$wishlist->getAuthor()->equals($user)) {
            return $this->redirectToRoute('app_list_wishlists', [], Response::HTTP_SEE_OTHER);
        } 

        $item = $itemRepository->findOneBy(['id' => $item_id]);
        if (empty($item)) {
            return $this->redirectToRoute('app_user_item_management', 
                                            ['username' => $username, 
                                            'wishlist_name' => $wishlist->getName()], 
                                            Response::HTTP_SEE_OTHER);        
        }

        if (!$this->isCsrfTokenValid('delete' . $item->getId(), $request->getPayload()->getString('_token'))) {            
            return $this->redirectToRoute('app_user_item_management', 
                                            ['username' => $username, 
                                            'wishlist_name' => $wishlist->getName()], 
                                            Response::HTTP_SEE_OTHER);
        }
        try {
            $wishlist->removeItemParams($item);
            $entityManager->flush();
            return $this->redirectToRoute('app_user_item_management', 
                                            ['username' => $username, 
                                            'wishlist_name' => $wishlist->getName()], 
                                             Response::HTTP_SEE_OTHER);
        } catch (Exception $e) {
            return $this->redirectToRoute('app_user_item_management', 
                                            ['username' => $username, 
                                            'wishlist_name' => $wishlist->getName()], 
                                            Response::HTTP_SEE_OTHER);
        }


    }



}