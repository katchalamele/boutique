<?php

namespace App\Cart;

use App\Repository\ProductRepository;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class CartService
{
    protected $session;
    protected $productRepository;
    public function __construct(SessionInterface $sessionInterface, ProductRepository $productRepository)
    {
        $this->session = $sessionInterface;
        $this->productRepository = $productRepository;
    }

    public function getCart(): array
    {
        return $this->session->get('cart', []);
    }

    public function saveCart($cart)
    {
        $this->session->set('cart', $cart);
    }

    public function add($id)
    {
        $cart = $this->getCart();

        if (array_key_exists($id, $cart)) $cart[$id]++;
        else $cart[$id] = 1;

        $this->saveCart($cart);
    }

    public function delete($id)
    {
        $cart = $this->getCart();
        unset($cart[$id]);
        $this->saveCart($cart);
    }

    public function decrement($id)
    {
        $cart = $this->getCart();
        if (isset($cart[$id])) {
            if ($cart[$id] > 1) $cart[$id]--;
            else {
                $this->delete($id);
                return;
            }

            $this->saveCart($cart);
        }
    }

    /**
     * @return CartItem[]
     */
    public function getCartItems(): array
    {
        $items = [];

        foreach ($this->getCart() as $id => $count) {
            $product = $this->productRepository->find($id);

            if (!$product) continue;

            $items[] = new CartItem($product, $count);
        }

        return $items;
    }

    public function getTotal(): int
    {
        $total = 0;

        foreach ($this->getCart() as $id => $count) {
            $product = $this->productRepository->find($id);

            if (!$product) continue;

            $cartItem = new CartItem($product, $count);
            $total +=  $cartItem->getTotal();
        }
        return $total;
    }

    public function emptyCart()
    {
        $this->saveCart([]);
    }

    public function isEmpty(): bool
    {
        return count($this->getCart()) === 0;
    }
}
