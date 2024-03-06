<?php

namespace App\Controller;

use App\Entity\CofSupplier;
use App\Repository\CofProductRepository;
use App\Repository\CofSupplierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SupplierController extends AbstractController
{
    /**
     * @Route("/{slug}", name="supplier_show")
     * 
     * @param CofSupplier $supplier
     * @return Response
     */
    public function index(string $slug, CofSupplierRepository $supplierRepository, CofProductRepository $productRepository): Response
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
