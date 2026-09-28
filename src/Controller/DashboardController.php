<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use App\Repository\LivreRepository;
use App\Repository\CommandeRepository;
use App\Repository\CategorieRepository;
use App\Repository\AuteurRepository;
use App\Repository\EditeurRepository;

#[IsGranted('ROLE_AGENT')]
#[Route('/dashboard', name: 'dashboard_home')]
final class DashboardController extends AbstractController
{
    public function __invoke(
        LivreRepository $livreRepository,
        CommandeRepository $commandeRepository,
        CategorieRepository $categorieRepository,
        AuteurRepository $auteurRepository,
        EditeurRepository $editeurRepository
    ): Response
    {
        return $this->render('dashboard/home.html.twig', [
            'stats' => [
                'livres' => $livreRepository->count([]),
                'commandes' => $commandeRepository->count([]),
                'categories' => $categorieRepository->count([]),
                'auteurs' => $auteurRepository->count([]),
                'editeurs' => $editeurRepository->count([]),
            ],
        ]);
    }
}
