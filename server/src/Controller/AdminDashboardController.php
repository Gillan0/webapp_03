<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AdminDashboardController extends AbstractController{
    #[Route('/admin/dashboard', name: 'app_admin_dashboard')]
    public function index(): Response
    {
        $items = [
            'item1' => ['lib_item' => 'Laptop', 'author' => 'John', 'price' => 2025],
            'item2' => ['lib_item' => 'Laptop', 'author' => 'John', 'price' => 2025],
            'item3' => ['lib_item' => 'Laptop', 'author' => 'John', 'price' => 2025],
        ];

        $wishlists = [
            'item1' => ['nb_purchased_item' => 11, 'author' => 'John', 'total_price' => 7000],
            'item2' => ['nb_purchased_item' => 8, 'author' => 'Sarah', 'total_price' => 4520],
            'item3' => ['nb_purchased_item' => 5, 'author' => 'Mike', 'total_price' => 2900],
        ];
        return $this->render('admin_dashboard/index.html.twig', [
            'controller_name' => 'AdminDashboardController',
            'items' => $items,
            'wishlists' => $wishlists,
        ]);
    }
}
