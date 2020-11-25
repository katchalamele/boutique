<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Form\CartConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class PurchaseConfirmationController extends AbstractController
{

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @isGranted("ROLE_USER",message="Vous devez être connecté pour passer la commande ")
     */
    public function confirm(CartService $cartService, EntityManagerInterface $em, Request $request)
    {
        $form = $this->createForm(CartConfirmType::class);
        $form->handleRequest($request);
        if (!$form->isSubmitted()) {
            $this->addFlash('warning', 'Vous devez remplir le formulaire avant de passer la commande.');
            return $this->redirectToRoute('cart_index');
        }

        $cartItems = $cartService->getCartItems();
        if (count($cartItems) == 0) {
            $this->addFlash('warning', 'Vous ne pouvez pas passer une commande avec un panier vide.');
            return $this->redirectToRoute('cart_index');
        }

        /** @var Purchase */
        $purchase = $form->getData();
        $purchase
            ->setUser($this->getUser())
            ->setPurchasedAt(new \DateTime())
            ->setTotal($cartService->getTotal());

        foreach ($cartItems as $cartItem) {
            $purchaseItem = new PurchaseItem;
            $purchaseItem
                ->setProduct($cartItem->product)
                ->setQuantity($cartItem->count)
                ->setProductName($purchaseItem->getProduct()->getName())
                ->setProductPrice($purchaseItem->getProduct()->getPrice())
                ->setTotal($purchaseItem->getProductPrice() * $purchaseItem->getQuantity())
                ->setPurchase($purchase);

            $em->persist($purchaseItem);
        }

        $em->persist($purchase);
        $em->flush();

        $cartService->emptyCart();

        $this->addFlash('success', 'La commande à bien été enregistrée');
        return $this->redirectToRoute('cart_index');
    }
}
