<?php

namespace App\Service;

use App\Entity\Livre;
use App\Repository\LivreRepository;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

final class CartService
{
    private const CART_KEY = 'cart_items';

    public function __construct(
        private RequestStack $requestStack,
        private LivreRepository $livreRepository
    ) {
    }

    /**
     * @return array<int, array{livre: Livre, quantite: int, total: string}>
     */
    public function getDetailedItems(): array
    {
        $items = [];
        foreach ($this->getCart() as $livreId => $quantite) {
            $livre = $this->livreRepository->find($livreId);
            if (!$livre) {
                continue;
            }
            $totalValue = (float) $livre->getPrix() * $quantite;
            $total = number_format($totalValue, 2, '.', '');
            $items[] = [
                'livre' => $livre,
                'quantite' => $quantite,
                'total' => $total,
            ];
        }

        return $items;
    }

    public function add(int $livreId, int $quantite = 1): void
    {
        $cart = $this->getCart();
        $cart[$livreId] = ($cart[$livreId] ?? 0) + max(1, $quantite);
        $this->getSession()->set(self::CART_KEY, $cart);
    }

    public function remove(int $livreId): void
    {
        $cart = $this->getCart();
        unset($cart[$livreId]);
        $this->getSession()->set(self::CART_KEY, $cart);
    }

    public function clear(): void
    {
        $this->getSession()->remove(self::CART_KEY);
    }

    /**
     * @return array<int, int>
     */
    public function getCart(): array
    {
        $cart = $this->getSession()->get(self::CART_KEY, []);

        return is_array($cart) ? $cart : [];
    }

    public function getTotal(): string
    {
        $totalValue = 0.0;
        foreach ($this->getDetailedItems() as $item) {
            $totalValue += (float) $item['total'];
        }

        return number_format($totalValue, 2, '.', '');
    }

    private function getSession(): SessionInterface
    {
        return $this->requestStack->getSession();
    }
}
