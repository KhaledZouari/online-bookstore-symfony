<?php

namespace App\Controller;

use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ABONNE')]
#[Route('/mes-commandes', name: 'app_order_history', methods: ['GET'])]
final class OrderHistoryController extends AbstractController
{
    public function __invoke(CommandeRepository $commandeRepository): Response
    {
        $user = $this->getUser();

        return $this->render('orders/history.html.twig', [
            'commandes' => $commandeRepository->findBy(['user' => $user], ['createdAt' => 'DESC']),
        ]);
    }
}
