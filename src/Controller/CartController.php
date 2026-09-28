<?php

namespace App\Controller;

use App\Entity\Commande;
use App\Entity\LigneCommande;
use App\Service\CartService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted('ROLE_ABONNE')]
#[Route('/panier', name: 'cart_')]
final class CartController extends AbstractController
{
    #[Route('', name: 'index', methods: ['GET'])]
    public function index(CartService $cartService): Response
    {
        return $this->render('cart/index.html.twig', [
            'items' => $cartService->getDetailedItems(),
            'total' => $cartService->getTotal(),
        ]);
    }

    #[Route('/ajouter/{id}', name: 'add', methods: ['POST'])]
    public function add(int $id, CartService $cartService, Request $request): Response
    {
        $quantite = $request->request->getInt('quantity', 1);
        $cartService->add($id, $quantite);

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/supprimer/{id}', name: 'remove', methods: ['POST'])]
    public function remove(int $id, CartService $cartService): Response
    {
        $cartService->remove($id);

        return $this->redirectToRoute('cart_index');
    }

    #[Route('/valider', name: 'confirm', methods: ['POST'])]
    public function confirm(CartService $cartService, EntityManagerInterface $entityManager): Response
    {
        $items = $cartService->getDetailedItems();
        if ($items === []) {
            $this->addFlash('warning', 'Votre panier est vide.');

            return $this->redirectToRoute('cart_index');
        }

        foreach ($items as $item) {
            $livre = $item['livre'];
            $stock = $livre->getStock();
            if ($stock < $item['quantite']) {
                $this->addFlash(
                    'warning',
                    'Stock insuffisant pour '.$livre->getTitre().'. Disponible: '.$stock.'.'
                );

                return $this->redirectToRoute('cart_index');
            }
        }

        $commande = new Commande();
        $commande->setUser($this->getUser());
        $commande->setCreatedAt(new \DateTimeImmutable());
        $commande->setStatut('EN_ATTENTE');
        $commande->setTotal($cartService->getTotal());

        foreach ($items as $item) {
            $ligne = new LigneCommande();
            $ligne->setCommande($commande);
            $ligne->setLivre($item['livre']);
            $ligne->setQuantite($item['quantite']);
            $ligne->setPrixUnitaire($item['livre']->getPrix());
            $commande->addLigneCommande($ligne);
            $item['livre']->setStock($item['livre']->getStock() - $item['quantite']);
        }

        $entityManager->persist($commande);
        $entityManager->flush();

        $cartService->clear();

        $this->addFlash('success', 'Commande confirmée.');

        return $this->redirectToRoute('cart_index');
    }
}
