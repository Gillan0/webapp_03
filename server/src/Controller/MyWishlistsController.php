<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Website;
use App\Entity\Wishlist;
use App\Entity\Item;
use \DateTime;
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

        $editForms = [];
        foreach ($wishlists as $wishlist) {
            $form =$this->createFormBuilder(new Wishlist())
            ->add('name', TextType::class)
            ->add('deadline', DateTimeType::class, [
                'widget' => 'single_text',
                'input' => 'datetime',
                'html5' => true,
            ])
            ->getForm();
            $editForms[$wishlist->getId()]= $form->createView(); 
        }

        $editingWishlistId = $request->query->get('editing_wishlist_id') ?? null;

        $form = $this->createFormBuilder(new Wishlist())
        ->add('name', TextType::class)
        ->add('deadline', DateTimeType::class, [
            'widget' => 'single_text',
            'input' => 'datetime',
            'html5' => true,
        ])
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
            $date = $formData->getDeadline();

            try {
                $wishlist = $user->createWishlist($name, $date);
                $entityManager->persist($user);
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
            'editForms' => $editForms,
            'editing_wishlist_id' => $editingWishlistId,
            'error' => $errorMessage,
        ]);

    }

    #[Route('/{username}/{wishlist_id}/delete', name: 'app_user_delete_wishlist', methods: ['POST'])]
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
        $wishlist = $wishlistRepository->findOneBy(['id' => $wishlist_id, 'author' => $user]);

        if (empty($wishlist)) {
            return $this->redirectToRoute('app_list_wishlists', ['username' => $username], Response::HTTP_SEE_OTHER);
        }

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
        
        $wishlist = $wishlistRepository->findOneBy(['id' => $wishlist_id, 'author' => $user]);

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
        
        $wishlist = $wishlistRepository->findOneBy(['id' => $wishlist_id, 'author' => $user]);

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


    #[Route('/invite/{sharing_uuid}', name: 'app_wishlist_shared', methods: ['GET','POST'])]
    public function share(Request $request, 
                            string $sharing_uuid, 
                            UserRepository $userRepository, 
                            WishlistRepository $wishlistRepository, 
                            EntityManagerInterface $entityManager): Response
    {
        $session = $request->getSession();
        $user = $session->get('user');

        if (empty($user)) {
            return $this->redirectToRoute('app_user_login', 
                                        [],
                                        Response::HTTP_SEE_OTHER);
        }

        try {
            $user = ConnexionController::handleSession($request, $user->getUsername(),  $userRepository);
        } catch (Exception $e) {
            return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
        }

        $wishlist = $wishlistRepository->findOneBy(["sharingUrl" => 'invite/'.$sharing_uuid]);

        if (empty($wishlist)) {    
            return $this->redirectToRoute('app_list_wishlists', 
                                        ['username' => $user->getUsername()],
                                        Response::HTTP_SEE_OTHER);
        }

        $author = $wishlist->getAuthor();

        try {
            $author->sendInvitation($user->getUsername(), $wishlist);
            $entityManager->persist($user);

            $entityManager->flush();

            return $this->render('case1/share_wishlist.html.twig', [
                'title' => 'Invited to '.$wishlist->getName()." by ".$author->getUsername(),
                'user' => $user,
                'wishlist' => $wishlist,
            ]);

        } catch (Exception $e) {
            return $this->redirectToRoute('app_list_wishlists', 
                                        ['username' => $user->getUsername()],
                                        Response::HTTP_SEE_OTHER);
        }
        
    }

    

    #[Route('/{username}/myWishlists/{wishlist_id}/edit', name: 'app_wishlist_edit', methods: ['GET', 'POST'])]  #A MODIF
    public function edit(   Request $request, 
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

        $wishlist_edited = $wishlistRepository->findOneBy(['id' => $wishlist_id]);

        $form = $this->createFormBuilder($wishlist_edited)
        ->add('name', TextType::class)
        ->add('deadline', DateTimeType::class, [
            'widget' => 'single_text',
            'input' => 'datetime',
            'html5' => true,
        ])
        ->getForm(); 

        $form->handleRequest($request);

        $errorMessage = null;

        if ($form->isSubmitted() && $form->isValid()) {

            
            $formData = $form->getData();
            $name = trim(htmlspecialchars($formData->getName()));
            $date = $formData->getDeadline();

            try {
                $wishlist_edited = $user->editWishlist($wishlist_edited, $name, $date);
    
                $entityManager->persist($wishlist_edited);
                $entityManager->flush();
            

                return $this->redirectToRoute('app_list_wishlists', ['username' => $username], Response::HTTP_SEE_OTHER);
                 
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
            }  
            
        }

        return $this->redirectToRoute('app_list_wishlists', 
                                        ['username' => $username, 
                                        'editing_wishlist_id' => $wishlist_id,
                                        'erreur'=> $errorMessage ], 
                                        Response::HTTP_SEE_OTHER);
    }

}