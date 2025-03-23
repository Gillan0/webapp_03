<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;


final class UserController extends AbstractController
{
    #[Route('/index', name: 'app_user_index', methods: ['GET'])]
    public function index(UserRepository $userRepository): Response
    {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User();
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/show', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/delete', name: 'app_user_delete', methods: ['POST'])]
    public function delete(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }


    ############### Controllers ajoutés ########################

    use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

    #[Route('/login', name: 'app_user_login', methods: ['GET', 'POST'])]
    public function login(UserRepository $userRepository, Request $request, UserPasswordEncoderInterface $passwordEncoder): Response
    {   
        $user = new User();
        $form = $this->createFormBuilder($user)
            ->add('email')
            ->add('password')
            ->getForm();
    
        $form->handleRequest($request);
    
        // Initialisation de la variable d'erreur
        $errorMessage = null;
    

        if ($form->isSubmitted() && $form->isValid()) {

            $data = $form->getData();
            $user = $userRepository->findUserByEmail($data->getEmail());
    
            // Si l'utilisateur existe dans la base de données
            if ($user !== null) {

                if ($passwordEncoder->isPasswordValid($user, $data->getPassword())) {

                    return $this->redirectToRoute('app_list_wishlists', [
                        'id' => $user->getId(),
                        'username' => $user->getUsername(),
                    ], Response::HTTP_SEE_OTHER);
                } else {

                    $errorMessage = "Invalid password!";
                }
            } else {

                $errorMessage = "You are not registered!";
            }
        }
    
        return $this->render('user/login.html.twig', [
            'form' => $form,
            'erreur' => $errorMessage,  // Passe l'erreur uniquement si elle existe
        ]);
    }
    

    #[Route('/signUp', name: 'app_user_sign_up', methods: ['GET','POST'])]
    public function signUp(Request $request, EntityManagerInterface $entityManager): Response
    {
        $user = new User(); 

        $form = $this->createFormBuilder()
        ->add('username', TextType::class, [
            'label' => 'Username',
        ])
        ->add('password', PasswordType::class, [
            'label' => 'Password',
        ])
        ->add('email', SubmitType::class, [
            'label' => 'Email',
        ])
        ->add('sign Up', SubmitType::class, [
            'label' => 'Create User',
        ])
        ->getForm(); 

        $form->handleRequest($request);


        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();
            $user -> setUsername($data->getSurname());
            $user -> setPassword($data->getPassword());
            $user -> setEmail($data->getEmail());
            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_list_wishlists', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/login.html.twig', [
            'form' => $form,
        ]);
    }


    #[Route('/{id}/myWishlists', name: 'app_list_wishlists', methods: ['GET','POST'])]
    public function list_wishlists(UserRepository $userRepository, User $user): Response
    {      
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
            $user->addWishlist($wishlist); 
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
            $user->removeInvitedWishlist($wishlist);
            $entityManager->flush();
            $entityManager->refresh($user);
        }

        return $this->redirectToRoute('app_list_wishlists', [], Response::HTTP_SEE_OTHER);
    }

    


    #[Route('/{id}/wishlist/add', name: 'app_user_wishlist_add', methods: ['GET','POST'])]
    public function add(Request $request, Wishlist $wishlist, EntityManagerInterface $entityManager): Response
    {
        // if ($this->isCsrfTokenValid('add'.$wishlist->getId(), $request->getPayload()->getString('_token'))) {
        //     $entityManager->remove($wishlist);
        //     $entityManager->flush();
        // }

        // return $this->redirectToRoute('app_wishlist_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/wishlist/shared', name: 'app_wishlist_shared', methods: ['GET','POST'])]
    public function share(Request $request, Wishlist $wishlist, EntityManagerInterface $entityManager): Response
    {
        // if ($this->isCsrfTokenValid('add'.$wishlist->getId(), $request->getPayload()->getString('_token'))) {
        //     $entityManager->remove($wishlist);
        //     $entityManager->flush();
        // }

        // return $this->redirectToRoute('app_wishlist_index', [], Response::HTTP_SEE_OTHER);
    }




}