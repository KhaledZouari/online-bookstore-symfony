<?php

namespace App\Controller;

use App\Repository\AuteurRepository;
use App\Repository\CategorieRepository;
use App\Repository\EditeurRepository;
use App\Repository\LivreRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/livres', name: 'front_livre_')]
final class FrontLivreController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(
        Request $request,
        LivreRepository $livreRepository,
        AuteurRepository $auteurRepository,
        EditeurRepository $editeurRepository,
        CategorieRepository $categorieRepository
    ): Response {
        $criteria = [
            'titre' => trim((string) $request->query->get('titre')),
            'auteur' => (int) $request->query->get('auteur', 0),
            'editeur' => (int) $request->query->get('editeur', 0),
            'categorie' => (int) $request->query->get('categorie', 0),
        ];

        $livres = $livreRepository->searchByCriteria($criteria);

        return $this->render('front/livres.html.twig', [
            'livres' => $livres,
            'criteria' => $criteria,
            'auteurs' => $auteurRepository->findBy([], ['nom' => 'ASC']),
            'editeurs' => $editeurRepository->findBy([], ['nom' => 'ASC']),
            'categories' => $categorieRepository->findBy([], ['nom' => 'ASC']),
        ]);
    }

    #[Route('/{id}', name: 'show', methods: ['GET'])]
    public function show(int $id, LivreRepository $livreRepository): Response
    {
        $livre = $livreRepository->find($id);
        if (!$livre) {
            throw $this->createNotFoundException('Livre introuvable.');
        }

        return $this->render('front/livre_show.html.twig', [
            'livre' => $livre,
        ]);
    }
}
