<?php

namespace App\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Security\Core\Security;

class PurchasePersister
{
    protected $security;
    protected $em;
    protected $cartService;

    public function __construct(Security $security, EntityManagerInterface $em, CartService $cartService)
    {
        $this->security = $security;
        $this->cartService = $cartService;
        $this->em = $em;
    }

    public function store(Purchase $purchase)
    {
        $purchase
            ->setUser($this->security->getUser());

        foreach ($this->cartService->getCartItems() as $cartItem) {
            $purchaseItem = new PurchaseItem;
            $purchaseItem
                ->setProduct($cartItem->product)
                ->setQuantity($cartItem->count)
                ->setProductName($purchaseItem->getProduct()->getName())
                ->setProductPrice($purchaseItem->getProduct()->getPrice())
                ->setTotal($purchaseItem->getProductPrice() * $purchaseItem->getQuantity())
                ->setPurchase($purchase);

            $this->em->persist($purchaseItem);
        }

        $this->em->persist($purchase);
        $this->em->flush();
    }
}
