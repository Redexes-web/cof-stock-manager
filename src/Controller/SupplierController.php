<?php

namespace App\Controller;

use App\Entity\Supplier;
use App\Repository\ProductRepository;
use App\Repository\SupplierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SupplierController extends AbstractController
{
    /**
     * @Route("/{slug}", name="supplier_show")
     * 
     * @param Supplier $supplier
     * @return Response
     */
    public function index(string $slug, SupplierRepository $supplierRepository, ProductRepository $productRepository): Response
    {
        $supplier = $supplierRepository->findOneBy(['slug' => $slug]);
        if (!$supplier)
            throw $this->createNotFoundException('The supplier does not exist');

        return $this->render('supplier/index.html.twig', [
            'supplier' => $supplier,
            'products' => $productRepository->findAll(),
        ]);
    }
}
