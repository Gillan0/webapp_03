<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wishlist;
use App\Entity\Item;
use App\Repository\UserRepository;
use App\Repository\WishlistRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\DateTimeType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * All routes needed for MyWishlists page (Case 1).
 * 
 * This includes :
 *  - viewing contributing, invited  and created wishlists 
 *  - adding, editing, deleting wishlists
 *  - accepting, refusing invitations
 * 
 * @author Antonino Gillard <antonino.gillard@imt-atlantique.net>
 * @author Lucien Duhamel <lucien.duhamel@imt-atlantique.net> 
 */
final class MyWishlistsController extends AbstractController
{


    #[Route('/{username}/myWishlists', name: 'app_list_wishlists', methods: ['GET','POST'])]
    public function show_list_wishlists(Request $request,string $username, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {      
        try {
            $user = ConnexionController::handleSession($request, $username,  $userRepository);
        } catch (Exception $e) {
            return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
        }

        $wishlists = $user->getWishlists(); 
        $invitedWishlists = $user->getInvitedWishlists(); 

        $form = $this->createFormBuilder(new Wishlist())
        ->add('name', TextType::class)
        ->add('deadline', DateTimeType::class)
        ->getForm(); 

        $errorMessage = null;

        try {            
            $form->handleRequest($request);
        } catch (Exception $e) {
            $errorMessage = $e->getMessage();
        }

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $name = trim(htmlspecialchars($formData->getName()));
            $date = trim(htmlspecialchars($formData->getDeadline()));

            try {
                $wishlist = $user->createWishlist($name, $date);
                $entityManager->persist($wishlist);
                $entityManager->flush();

                return $this->redirectToRoute('app_list_wishlists',
                                             ["username" => $user->getUsername()], 
                                             Response::HTTP_SEE_OTHER);
            } catch (Exception $e) {
                $errorMessage = $e->getMessage(); 
            }    
        }


        return $this->render('case1/list_wishlists.html.twig', [
            'title' => $user->getUsername().' - My Wishlists',
            'user'=>$user,
            'wishlists' => $wishlists,
            'invitedWishlists' => $invitedWishlists,
            'form' => $form,
            'error' => $errorMessage,
        ]);

    }

    #[Route('/{username}/{wishlist_id}', name: 'app_user_delete_wishlist', methods: ['POST'])]
    public function delete(Request $request,
                            string $username, 
                            string $wishlist_id, 
                            UserRepository $userRepository, 
                            WishlistRepository $wishlistRepository,
                            EntityManagerInterface $entityManager): Response
    {
        try {
            $user = ConnexionController::handleSession($request, $username,  $userRepository);
        } catch (Exception $e) {
            return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
        }
        $wishlist = $wishlistRepository->findOneBy(['id' => $wishlist_id]);

        if (!$this->isCsrfTokenValid('delete' . $wishlist->getId(), $request->getPayload()->getString('_token'))) {            
            return $this->redirectToRoute('app_list_wishlists', ['username' => $username], Response::HTTP_SEE_OTHER);
        }

        try {
            $user->deleteWishlist($wishlist);
            $entityManager->flush();

            return $this->redirectToRoute('app_list_wishlists', 
                                            ['username' => $username],
                                             Response::HTTP_SEE_OTHER);

        } catch (Exception $e) {
            return $this->redirectToRoute('app_list_wishlists', 
                                            ['username' => $username],
                                             Response::HTTP_SEE_OTHER);
        }
    }

    
    #[Route('/{username}/myWishlists/invitationAccepted/{wishlist_id}', name: 'app_user_wishlist_accepted', methods: ['GET', 'POST'])]
    public function accept(Request $request, 
                            string $username, 
                            string $wishlist_id, 
                            UserRepository $userRepository, 
                            WishlistRepository $wishlistRepository,
                            EntityManagerInterface $entityManager): Response
    {
        try {
            $user = ConnexionController::handleSession($request, $username,  $userRepository);
        } catch (Exception $e) {
            return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
        }        
        
        $wishlist = $wishlistRepository->findOneBy(['id' => $wishlist_id]);

        if (!$this->isCsrfTokenValid('accept'.$user->getId(), $request->getPayload()->getString('_token'))) {
            return $this->redirectToRoute('app_list_wishlists', ['username' => $username], Response::HTTP_SEE_OTHER);
        }

        try {
            $user->acceptInvitation($wishlist);
            $entityManager->flush();
        } catch (Exception $e) {}
        
        return $this->redirectToRoute('app_list_wishlists', 
                                        ['username' => $username],
                                        Response::HTTP_SEE_OTHER);

    }

    #[Route('/{username}/myWishlists/invitationRefused/{wishlist_id}', name: 'app_user_wishlist_refused', methods: ['GET', 'POST'])]
    public function refuse(Request $request, 
                            string $username, 
                            string $wishlist_id, 
                            UserRepository $userRepository, 
                            WishlistRepository $wishlistRepository,
                            EntityManagerInterface $entityManager): Response
    {
        try {
            $user = ConnexionController::handleSession($request, $username,  $userRepository);
        } catch (Exception $e) {
            return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
        }
        
        $wishlist = $wishlistRepository->findOneBy(['id' => $wishlist_id]);

        if (!$this->isCsrfTokenValid('refuse'.$user->getId(), $request->getPayload()->getString('_token'))) {
            return $this->redirectToRoute('app_list_wishlists', ['username' => $username], Response::HTTP_SEE_OTHER);
        }

        try {
            $user->refuseInvitation($wishlist);
            $entityManager->flush();
        } catch (Exception $e) {}
        
        return $this->redirectToRoute('app_list_wishlists', 
                                        ['username' => $username],
                                        Response::HTTP_SEE_OTHER);

    }   



    #[Route('/{username}/wishlist/add', name: 'app_user_wishlist_add', methods: ['GET','POST'])]
    public function add(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        
        if ($this->isCsrfTokenValid('add'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_wishlist_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{username}/{wishlistId}/shared', name: 'app_wishlist_shared', methods: ['GET','POST'])]
    public function share(Request $request, string $username, string $wishlistId, UserRepository $userRepository, WishlistRepository $wishlistRepository, EntityManagerInterface $entityManager): Response
    {

        $user = $userRepository->findOneBy(['username' => $username]);
        $wishlist = $wishlistRepository->findOneBy(['id' => $wishlistId]);

        if ($this->isCsrfTokenValid('share'.$user->getId(), $request->getPayload()->getString('_token'))) {
            
            $sharingUrl = $wishlist->getSharingUrl();
            return $this->render('case1/list_wishlists.html.twig', [
                'title' => 'MyWishlists Page',
                'user' => $user,
                'wishlists' => $user->getWishlists(),
                'invitedWishlists' => $user->getInvitedWishlists(),
                'authors' => $wishlist->getAuthor(),
                'sharingUrl' => $sharingUrl,
            ]);
        }
        
        
        return $this->render('case1/list_wishlists.html.twig', [
            'title' => 'MyWishlists Page',
            'user' => $user,
            'wishlists' => $user->getWishlists(),
            'invitedWishlists' => $user->getInvitedWishlists(),
            'authors' => $wishlist->getAuthor(),
            'erreur' => "Invalid CSRF token.",
        ]);
        
    }

    


    #[Route('/{username}/{wishlistId}/displayed', name: 'app_wishlist_displayed', methods: ['GET','POST'])]
    public function display(Request $request, string $username, string $wishlistId, UserRepository $userRepository, WishlistRepository $wishlistRepository, EntityManagerInterface $entityManager): Response
    {
        
        $user = $userRepository->findOneBy(['username' => $username]);
        $wishlist = $wishlistRepository->findOneBy(['id' => $wishlistId]);

        if ($this->isCsrfTokenValid('display'.$user->getId(), $request->getPayload()->getString('_token'))) {
            
            $displayUrl = $wishlist->getDisplayUrl();
            return $this->render('case1/list_wishlists.html.twig', [
                'title' => 'MyWishlists Page',
                'user' => $user,
                'wishlists' => $user->getWishlists(),
                'invitedWishlists' => $user->getInvitedWishlists(),
                'authors' => $wishlist->getAuthor(),
                'displayUrl' => $displayUrl,
            ]);
        }

        return $this->render('case1/list_wishlists.html.twig', [
            'title' => 'MyWishlists Page',
            'user' => $user,
            'wishlists' => $user->getWishlists(),
            'invitedWishlists' => $user->getInvitedWishlists(),
            'authors' => $wishlist->getAuthor(),
            'erreur' => "Invalid CSRF token.",
        ]);
    }


    #[Route('/{username}/myWishlists/edit', name: 'app_wishlist_edit', methods: ['GET', 'POST'])]  #A MODIF
    public function edit(Request $request, Wishlist $wishlist, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(WishlistType::class, $wishlist);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_wishlist_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('wishlist/edit.html.twig', [
            'wishlist' => $wishlist,
            'form' => $form,
        ]);
    }

}