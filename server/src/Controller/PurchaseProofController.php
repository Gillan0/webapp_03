<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PurchaseProofController extends AbstractController
{
    #[Route('/purchase/proof', name: 'app_purchase_proof')]
    public function index(): Response
    {
        return $this->render('purchase_proof/purchase_proof.html.twig', [
            'controller_name' => 'PurchaseProofController',
        ]);
    }
}
