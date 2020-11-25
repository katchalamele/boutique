<?php

namespace App\Controller\Purchase;

use App\Cart\CartService;
use App\Form\CartConfirmType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;
use App\Entity\Purchase;
use App\Entity\PurchaseItem;
use App\Purchase\PurchasePersister;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class PurchaseConfirmationController extends AbstractController
{

    /**
     * @Route("/purchase/confirm", name="purchase_confirm")
     * @isGranted("ROLE_USER",message="Vous devez être connecté pour passer la commande ")
     */
    public function confirm(CartService $cartService, Request $request, PurchasePersister $purchasePersister)
    {
        //Récupération des données du formulaire
        $form = $this->createForm(CartConfirmType::class);
        $form->handleRequest($request);
        if (!$form->isSubmitted()) {
            $this->addFlash('warning', 'Vous devez remplir le formulaire avant de passer la commande.');
            return $this->redirectToRoute('cart_index');
        }

        //Récuperation des produits du panier
        if ($cartService->isEmpty()) {
            $this->addFlash('warning', 'Vous ne pouvez pas passer une commande avec un panier vide.');
            return $this->redirectToRoute('cart_index');
        }

        //Création de la commande
        /** @var Purchase */
        $purchase = $form->getData();
        $purchasePersister->store($purchase);

        $this->addFlash('success', 'La commande à bien été enregistrée');
        return $this->redirectToRoute('purchase_payment_form', [
            'id' => $purchase->getId()
        ]);
    }
}
