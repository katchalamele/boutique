<?php

namespace App\Controller;

use App\Repository\ProductRepository;
use App\Cart\CartService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class CartController extends AbstractController
{
    protected $cartService;

    public function __construct(CartService $cartService)
    {
        $this->cartService = $cartService;
    }

    /**
     * @Route("/cart/add/{id}", name="cart_add", requirements={"id":"\d+"})
     */
    public function add($id, Request $request, ProductRepository $productRepository): Response
    {
        $product = $productRepository->find($id);

        if (!$product) throw $this->createNotFoundException('Le produit demandé n\'existe pas');

        $this->cartService->add($id);

        $this->addFlash('success', 'Le produit à été ajouté dans le panier');

        return $this->redirect($request->headers->get('referer'));
    }

    /**
     * @Route("/cart", name="cart_index")
     */
    public function index()
    {
        $items = $this->cartService->getCartItems();
        $total = $this->cartService->getTotal();

        return $this->render('cart/index.html.twig', [
            'items' => $items,
            'total' => $total
        ]);
    }

    /**
     * @Route("/cart/delete/{id}", name="cart_delete", requirements={"id":"\d+"})
     */
    public function delete($id)
    {
        $this->cartService->delete($id);
        return $this->redirectToRoute('cart_index');
    }

    /**
     * @Route("/cart/decrement/{id}", name="cart_decrement", requirements={"id":"\d+"})
     */
    public function decrement($id)
    {
        $this->cartService->decrement($id);
        return $this->redirectToRoute('cart_index');
    }
}
