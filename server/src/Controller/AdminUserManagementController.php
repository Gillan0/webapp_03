<?php

namespace App\Controller;

use App\Entity\Admin;
use App\Entity\User;
use App\Repository\UserRepository;
use App\Repository\WebsiteRepository;
use Doctrine\ORM\EntityManagerInterface;
use Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class AdminUserManagementController extends AbstractController
{
    #[Route('/{username}/admin/users', name: 'app_admin_users', methods: ['GET'])]
    public function index(Request $request, string $username, UserRepository $userRepository, WebsiteRepository $websiteRepository): Response
    {
        try {
            $admin = ConnexionController::handleSession($request, $username, $userRepository);
            // Verify user is admin
            if (!($admin instanceof Admin)) {
                return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
            }
        } catch (Exception $e) {
            return $this->redirectToRoute('app_admin_login', [], Response::HTTP_SEE_OTHER);
        }

        // Get all users
        $users = $userRepository->findAll();

        return $this->render('admin_dashboard/user_management.html.twig', [
            'users' => $users,
            'admin' => $admin,
        ]);
    }

    #[Route('/{username}/admin/users/lock/{id}', name: 'app_admin_user_lock', methods: ['GET'])]
    public function lockUser(Request $request, string $username, int $id, UserRepository $userRepository, WebsiteRepository $websiteRepository, EntityManagerInterface $entityManager): Response
    {
        try {
            $admin = ConnexionController::handleSession($request, $username, $userRepository);
            // Verify user is admin
            if (!($admin instanceof Admin)) {
                return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
            }
        } catch (Exception $e) {
            return $this->redirectToRoute('app_admin_login', [], Response::HTTP_SEE_OTHER);
        }

        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $website = $websiteRepository->findOneBy(['id' => 1]);
        if (!$website) {
            throw $this->createNotFoundException('Website not found');
        }

        $website->lockUser($admin, $user);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_users', ['username' => $admin->getUsername()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{username}/admin/users/unlock/{id}', name: 'app_admin_user_unlock', methods: ['GET'])]
    public function unlockUser(Request $request, string $username, int $id, UserRepository $userRepository, WebsiteRepository $websiteRepository, EntityManagerInterface $entityManager): Response
    {
        try {
            $admin = ConnexionController::handleSession($request, $username, $userRepository);
            // Verify user is admin
            if (!($admin instanceof Admin)) {
                return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
            }
        } catch (Exception $e) {
            return $this->redirectToRoute('app_admin_login', [], Response::HTTP_SEE_OTHER);
        }

        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $website = $websiteRepository->findOneBy(['id' => 1]);
        if (!$website) {
            throw $this->createNotFoundException('Website not found');
        }

        $website->unlockUser($admin, $user);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_users', ['username' => $admin->getUsername()], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{username}/admin/users/delete/{id}', name: 'app_admin_user_delete', methods: ['GET'])]
    public function deleteUser(Request $request, string $username, int $id, UserRepository $userRepository, WebsiteRepository $websiteRepository, EntityManagerInterface $entityManager): Response
    {
        try {
            $admin = ConnexionController::handleSession($request, $username, $userRepository);
            // Verify user is admin
            if (!($admin instanceof Admin)) {
                return $this->redirectToRoute('app_user_login', [], Response::HTTP_SEE_OTHER);
            }
        } catch (Exception $e) {
            return $this->redirectToRoute('app_admin_login', [], Response::HTTP_SEE_OTHER);
        }

        $user = $userRepository->find($id);
        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $website = $websiteRepository->findOneBy(['id' => 1]);
        if (!$website) {
            throw $this->createNotFoundException('Website not found');
        }

        $website->deleteUser($admin, $user);
        $entityManager->flush();

        return $this->redirectToRoute('app_admin_users', ['username' => $admin->getUsername()], Response::HTTP_SEE_OTHER);
    }
}