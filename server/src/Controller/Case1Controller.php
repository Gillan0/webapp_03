<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\WebsiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
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


        if ($form->isSubmitted() && $form->isValid()) {

            // Unpack and filter data against XSS attacks
            $formData = $form->getData();
            $username = trim(htmlspecialchars($formData->getUsername()));
            $password = trim(htmlspecialchars($formData->getPassword()));
            $email = trim(htmlspecialchars($formData->getEmail()));

            $website = $websiteRepository->findOneBy(['id' => 1]);  

            $user = $website->createUser($username, $password, $email);

            $entityManager->persist($website);
            $entityManager->persist($user);

            $entityManager->flush();

            return $this->redirectToRoute('app_list_wishlists', ['username' => $user->getUsername()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/sign_up.html.twig', [
            'title' => 'Sign Up Page',
            'form' => $form,
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

        if ($form->isSubmitted() && $form->isValid()) {
            $formData = $form->getData();
            $username = trim(htmlspecialchars($formData->getUsername()));
            $password = trim(htmlspecialchars($formData->getPassword()));


            $website = $websiteRepository->findOneBy(['id' => 1]);  

            $user = $website->login($username, $password);
            
            if ($user!=NULL){
                return $this->redirectToRoute('app_list_wishlists', ["username" => $user->getUsername()], Response::HTTP_SEE_OTHER);
            }
        }
        $e = new Exception("You are not registered.");
        return $this->render('user/login.html.twig', [
            'title' => 'Login Page',
            'form' => $form,
            'erreur' => $e,
        ]);
    }


    #[Route('/{username}/myWishlists', name: 'app_list_wishlists', methods: ['GET','POST'])]
    public function show_list_wishlists(string $username, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {      
        $user = $userRepository->findOneBy(['username' => $username]);
        $wishlists = $user->getWishlists(); 
        $invitedWishlists = $user->getInvitedWishlists(); 
        $authors = array();
        foreach ($wishlists as $wishlist){
            $authors = $wishlist->getAuthor();
        }

        return $this->render('wishlist/list_wishlists.html.twig', [
            'user'=>$user,
            'wishlists' => $wishlists,
            'invitedWishlists' => $invitedWishlists,
            'authors' => $authors,
        ]);

    }

    
    #[Route('/{username}/myWishlists/invitationAccepted', name: 'app_user_wishlist_accepted', methods: ['GET', 'POST'])]
    public function accept(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('accept'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $wishlist = $request->request->get('invitedWishlist');
            $user->acceptInvitation($wishlist);
            $entityManager->flush();
            $entityManager->refresh($user);
        }

        return $this->redirectToRoute('app_list_wishlists', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{username}/myWishlists/invitationRefused', name: 'app_user_wishlist_refused', methods: ['GET', 'POST'])]
    public function refuse(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('refuse'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $wishlist = $request->request->get('invitedWishlist');
            $user->refuseInvitation($wishlist);
            $entityManager->flush();
            $entityManager->refresh($user);
        }

        return $this->redirectToRoute('app_list_wishlists', [], Response::HTTP_SEE_OTHER);
    }    

}