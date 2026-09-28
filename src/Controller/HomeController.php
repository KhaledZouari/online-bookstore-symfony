<?php

namespace App\Controller;

use App\Repository\CategorieRepository;
use App\Repository\EditeurRepository;
use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/', name: 'front_home', methods: ['GET'])]
final class HomeController extends AbstractController
{
    public function __invoke(
        CategorieRepository $categorieRepository,
        EditeurRepository $editeurRepository,
        LivreRepository $livreRepository
    ): Response {
        return $this->render('front/home.html.twig', [
            'categories' => $categorieRepository->findBy([], ['nom' => 'ASC']),
            'editeurs' => $editeurRepository->findBy([], ['nom' => 'ASC']),
            'livres' => $livreRepository->findBy([], ['id' => 'DESC'], 8),
        ]);
    }
}
