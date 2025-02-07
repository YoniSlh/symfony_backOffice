<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\CsvExporter;

#[Route('/admin/products')]
class AdminProductController extends AbstractController
{
    #[Route('/', name: 'admin_products')]
    public function index(ProductRepository $productRepository, Request $request): Response
    {
        $sort = $request->query->get('sort');

        if ($sort === 'price_desc') {
            $products = $productRepository->findAllSortedByPriceDesc();
        } elseif ($sort === 'price_asc') {
            $products = $productRepository->findAllSortedByPriceAsc();
        } else {
            $products = $productRepository->findAll();
        }

        return $this->render('admin_product/index.html.twig', [
            'products' => $products,
            'currentSort' => $sort,
        ]);
    }

    #[Route('/new', name: 'admin_products_new')]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $product = new Product();
        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($product);
            $entityManager->flush();

            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin_product/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'admin_products_edit')]
    public function edit(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_EDIT', $product);

        $form = $this->createForm(ProductType::class, $product);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            return $this->redirectToRoute('admin_products');
        }

        return $this->render('admin_product/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/delete/{id}', name: 'admin_products_delete', methods: ['POST'])]
    public function delete(Request $request, Product $product, EntityManagerInterface $entityManager): Response
    {
        $this->denyAccessUnlessGranted('PRODUCT_DELETE', $product);

        if ($this->isCsrfTokenValid('delete' . $product->getId(), $request->request->get('_token'))) {
            $entityManager->remove($product);
            $entityManager->flush();
        }

        return $this->redirectToRoute('admin_products');
    }

    #[Route('/export', name: 'admin_products_export')]
    public function exportCsv(CsvExporter $csvExporter, ProductRepository $productRepository): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $products = $productRepository->findAll();
        $csvContent = $csvExporter->export($products);

        $response = new Response($csvContent);
        $response->headers->set('Content-Type', 'text/csv');
        $response->headers->set('Content-Disposition', 'attachment; filename="products.csv"');

        return $response;
    }
}
