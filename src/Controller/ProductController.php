<?php

namespace App\Controller;

use App\Entity\Product;
use App\Event\ProductViewEvent;
use App\Form\ProductType;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\String\Slugger\SluggerInterface;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;

class ProductController extends AbstractController
{
    /**
     * @Route("/{slug}", name="product_category", priority=-1)
     */
    public function category(CategoryRepository $categoryRepository, $slug): Response
    {
        $category = $categoryRepository->findOneBy(['slug' => $slug]);

        if (!$category) throw $this->createNotFoundException("Cette categorie n'existe pas :(");

        return $this->render('product/category.html.twig', [
            'category' => $category,
        ]);
    }

    /**
     * @Route("/{category_slug}/{slug}", name="product_show", priority=-1)
     */
    public function show(ProductRepository $productRepository, $slug, EventDispatcherInterface $eventDispatcher)
    {
        $product = $productRepository->findOneBy(['slug' => $slug]);

        if (!$product) throw $this->createNotFoundException('Le produit demandé n\'existe pas :(');

        $productEvent = new ProductViewEvent($product);
        $eventDispatcher->dispatch($productEvent, 'product.view');

        return $this->render('product/show.html.twig', [
            'product' => $product
        ]);
    }

    /**
     * @Route("/admin/product/create", name="product_create")
     */
    public function create(Request $request, SluggerInterface $sluggerInterface, EntityManagerInterface $em)
    {
        $product = new Product;
        $form = $this->createForm(ProductType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            //$product->setSlug(strtolower($sluggerInterface->slug($product->getName())));
            $em->persist($product);
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        $formView = $form->createView();

        return $this->render('product/create.html.twig', [
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/admin/product/{id}/edit", name="product_edit")
     */
    public function edit($id, ProductRepository $productRepository, Request $request, EntityManagerInterface $em)
    {
        $product = $productRepository->find($id);

        if (!$product) throw $this->createNotFoundException('Ce produit n\'existe pas');

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $em->flush();

            return $this->redirectToRoute('product_show', [
                'category_slug' => $product->getCategory()->getSlug(),
                'slug' => $product->getSlug()
            ]);
        }

        $formView = $form->createView();
        return $this->render('product/edit.html.twig', [
            'product' => $product,
            'formView' => $formView
        ]);
    }

    /**
     * @Route("/product/index", name="product_index")
     */
    public function index(ProductRepository $productRepository)
    {
        $products = $productRepository->findAll();
        shuffle($products);
        return $this->render('product/index.html.twig', [
            'products' => $products
        ]);
    }

    /**
     * @Route("/product/delete/{id}", name="product_delete", requirements={"id":"\d+"})
     * @isGranted("ROLE_SUPER_ADMIN", message="Vous devez être super admin pour supprimer un produit")
     */
    public function delete($id)
    {
        # code...
    }
}
