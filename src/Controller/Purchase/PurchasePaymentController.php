<?php

namespace App\Controller\Purchase;

use App\Entity\Purchase;
use App\Cart\CartService;
use App\Repository\PurchaseRepository;
use App\Stripe\StripeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;

class PurchasePaymentController extends AbstractController
{

    protected $purchaseRepository;
    protected $cartService;
    protected $em;

    public function __construct(PurchaseRepository $purchaseRepository, CartService $cartService, EntityManagerInterface $em)
    {
        $this->purchaseRepository = $purchaseRepository;
        $this->cartService = $cartService;
        $this->em = $em;
    }

    /**
     * @Route("/purchase/pay/{id}", name="purchase_payment_form", requirements={"id":"\d+"})
     * @IsGranted("ROLE_USER", message="Vous devez d'abord vous connecter")
     */
    public function showCardForm($id, StripeService $stripeService)
    {
        $purchase = $this->purchaseRepository->find($id);

        if (!$purchase || ($purchase->getUser() !== $this->getUser()) || ($purchase->getStatus() === Purchase::STATUS_PAID)) {
            $this->addFlash('warning', 'La commande n\'existe pas.');
            return $this->redirectToRoute('cart_index');
        }

        $intent = $stripeService->getPaymentIntent($purchase);

        $this->addFlash('warning', 'Attention ceci est un formulaire de paiement de test, ne renseignez pas les information de votre carte de paiement.');
        $this->addFlash('info', 'Essayez avec ces fausses informations: N°carte: 4242 4242 4242 4242 | EXP: 04/24 | CVC: 242 | codePostale: 42424');
        return $this->render('purchase/payment.html.twig', [
            'clientSecret' => $intent->client_secret,
            'purchase' => $purchase,
            'stripePublicKey' => $stripeService->getPublicKey()
        ]);
    }

    /**
     * @Route("/purchase/pay/{id}/success", name="purchase_payment_success", requirements={"id":"\d+"})
     * @IsGranted("ROLE_USER", message="Vous devez d'abord vous connecter")
     */
    public function success($id)
    {
        $purchase = $this->purchaseRepository->find($id);
        if (!$purchase || ($purchase->getUser() !== $this->getUser()) || ($purchase->getStatus() === Purchase::STATUS_PAID)) {
            $this->addFlash('warning', 'La commande n\'existe pas.');
            return $this->redirectToRoute('cart_index');
        }

        $purchase->setStatus(Purchase::STATUS_PAID);
        $this->em->flush();
        $this->cartService->emptyCart();

        $this->addFlash('success', 'Votre payement s\'est effectué avec succès');
        return $this->redirectToRoute('purchase_index');
    }
}
