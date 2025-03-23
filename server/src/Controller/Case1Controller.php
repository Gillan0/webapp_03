<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Wishlist;
use App\Entity\Item;
use App\Repository\UserRepository;
use App\Repository\WishlistRepository;
use App\Repository\WebsiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\UrlType;
use Symfony\Component\Form\Extension\Core\Type\NumberType;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class Case1Controller extends AbstractController
{

    #[Route('/signUp', name: 'app_user_sign_up', methods: ['GET','POST'])]
    public function signUp(Request $request, EntityManagerInterface $entityManager, WebsiteRepository $websiteRepository): Response
    {
        $user = new User();

        $form = $this->createFormBuilder($user)
        ->add('username')
        ->add('password', PasswordType::class)
        ->add('email')
        ->getForm();
        $form->handleRequest($request);

        $errorMessage = null;

        if ($form->isSubmitted() && $form->isValid()) {

            // Unpack and filter data against XSS attacks
            $formData = $form->getData();
            $username = trim(htmlspecialchars($formData->getUsername()));
            $password = trim(htmlspecialchars($formData->getPassword()));
            $email = trim(htmlspecialchars($formData->getEmail()));

            $website = $websiteRepository->findOneBy(['id' => 1]);  

            try {
                $user = $website->createUser($username, $password, $email);

                $entityManager->persist($website);
                $entityManager->persist($user);

                $entityManager->flush();

                return $this->redirectToRoute('app_list_wishlists', ['username' => $user->getUsername()], Response::HTTP_SEE_OTHER);
            } catch (Exception $e) {
                $errorMessage = $e->getMessage();
            }
        }

        return $this->render('case1/sign_up.html.twig', [
            'title' => 'Sign Up Page',
            'form' => $form,
            'erreur' => $errorMessage,
        ]);
    }


    #[Route('/login', name: 'app_user_login', methods:  ['GET','POST'])]
    public function login(WebsiteRepository $websiteRepository, Request $request, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createFormBuilder(new User())
        ->add('username', TextType::class)
        ->add('password', PasswordType::class)
        ->getForm(); 

        $form->handleRequest($request);

        $errorMessage = null;

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $username = trim(htmlspecialchars($formData->getUsername()));
            $password = trim(htmlspecialchars($formData->getPassword()));


            $website = $websiteRepository->findOneBy(['id' => 1]);  

            try {
                $user = $website->login($username, $password);
                return $this->redirectToRoute('app_list_wishlists', ["username" => $user->getUsername()], Response::HTTP_SEE_OTHER);
            } catch (Exception $e) {
                $errorMessage = $e->getMessage(); 
            }    
        }

        return $this->render('case1/login.html.twig', [
            'title' => 'Login Page',
            'form' => $form,
            'error' => $errorMessage,
        ]);
    }


    #[Route('/{username}/myWishlists', name: 'app_list_wishlists', methods: ['GET','POST'])]
    public function show_list_wishlists(string $username, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {      
        $user = $userRepository->findOneBy(['username' => $username]);
        $wishlists = $user->getWishlists(); 
        $invitedWishlists = $user->getInvitedWishlists(); 

        return $this->render('case1/list_wishlists.html.twig', [
            'title' => 'My Wishlists',
            'user'=>$user,
            'wishlists' => $wishlists,
            'invitedWishlists' => $invitedWishlists
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
        $user = $userRepository->findOneBy(['username' => $username]);
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
        $user = $userRepository->findOneBy(['username' => $username]);
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
        $user = $userRepository->findOneBy(['username' => $username]);
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

    #[Route('/{username}/itemManagement/{wishlist_name}', name: 'app_user_item_management', methods: ['GET','POST'])]
    public function manageWishlist(Request $request, 
                        string $username, 
                        string $wishlist_name, 
                        UserRepository $userRepository, 
                        WishlistRepository $wishlistRepository,
                        EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->findOneBy(['username' => $username]);
        $wishlist = $wishlistRepository->findOneBy(['name' => $wishlist_name]);

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