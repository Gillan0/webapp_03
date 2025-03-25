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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

/**
 * All routes and util methods needed to handle connexion (Case 1)
 * 
 * This includes Login and Sign Up pages and session management.
 * 
 * @author Antonino Gillard <antonino.gillard@imt-atlantique.net>
 * @author Lucien Duhamel <lucien.duhamel@imt-atlantique.net> 
 */
final class ConnexionController extends AbstractController
{

    #[Route('/', name: 'app_default', methods: ['GET','POST'])]
    /**
     * Redirect base url to /login 
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Repository\WebsiteRepository $websiteRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function home(Request $request, EntityManagerInterface $entityManager, WebsiteRepository $websiteRepository): Response
    {
        return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
    }


    #[Route('/signUp', name: 'app_user_sign_up', methods: ['GET','POST'])]
    /**
     * SignUp screen 
     * 
     * @param \Symfony\Component\HttpFoundation\Request $request
     * @param \Doctrine\ORM\EntityManagerInterface $entityManager
     * @param \App\Repository\WebsiteRepository $websiteRepository
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function signUp(Request $request, EntityManagerInterface $entityManager, WebsiteRepository $websiteRepository): Response
    {
        // Build new user
        $user = new User();

        // Form createion
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

                $session = $request->getSession();
                $session->set('user', $user);

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

                $session = $request->getSession();
                $session->set('user', $user);

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

    #[Route('/logout', name: 'app_user_logout', methods:  ['GET','POST'])]
    public function logout(Request $request): Response
    {
        $session = $request->getSession();
        $session->clear();

        return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
    }

    public static function handleSession(Request $request, string $username, UserRepository $userRepository,) {
        $user = $request->getSession()->get('user');
        $expectedUser = $userRepository->findOneBy(['username' => $username]);

        if (empty($user) || empty($expectedUser)) {
            throw new Exception("No session / Corresponding user");
        }

        // Incorrect session
        if (!$user->equals($expectedUser)) {
            throw new Exception("Session doesn't match expected user");
        }
        
        // Update session with latest user object
        $user = $expectedUser;
        $request->getSession()->set('user', $user);

        if ($user->isLocked()) {
            throw new Exception("Account has been locked by admin");
        }

        return $user;
    }

}