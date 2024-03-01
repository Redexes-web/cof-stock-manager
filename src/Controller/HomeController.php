<?php

namespace App\Controller;

use App\Repository\CofProductRepository;
use App\Repository\CofSupplierRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(CofSupplierRepository $supplierRepository, CofProductRepository $productRepository): Response
    {
        return $this->render('home/index.html.twig', [
            'suppliers' => $supplierRepository->findAll(),
            'products' => $productRepository->findAll(),
        ]);
    }
}
