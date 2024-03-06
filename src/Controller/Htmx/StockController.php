<?php

namespace App\Controller\Htmx;

use App\Entity\Sell;
use App\Entity\CofStock;
use App\Entity\CofProduct;
use App\Entity\CofSupplier;
use App\Repository\SellRepository;
use App\Repository\CofStockRepository;
use App\Repository\CofProductRepository;
use App\Repository\CofSupplierRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class StockController extends AbstractController
{

    /**
     * @Route("htmx/stock/decrease/{id}", name="htmx_stock_decrease")
     * 
     * @param CofSupplier $supplier
     * @return Response
     */
    public function htmx_stock_decrease(
        int $id,
        CofProductRepository $productRepository,
        CofSupplierRepository $supplierRepository,
        CofStockRepository $stockRepository
    ): Response {
        /**
         * @var ?CofStock $stock
         */
        $stock = $stockRepository->find($id);
        if (!$stock)
            throw $this->createNotFoundException('The stock does not exist');
        $stock->setStock($stock->getStock() - 1);
        if ($stock->getStock() <= 0)
            $stockRepository->remove($stock, true);
        else
            $stockRepository->add($stock, true);
        return $this->render('htmx/stock/show.html.twig', [
            'stock' => $stock,
        ]);
    }

    /**
     * @Route("htmx/stock/increase/{id}", name="htmx_stock_increase")
     * 
     * @param CofSupplier $supplier
     * @return Response
     */
    public function htmx_stock_increase(
        int $id,
        CofProductRepository $productRepository,
        CofSupplierRepository $supplierRepository,
        CofStockRepository $stockRepository
    ): Response {
        /**
         * @var ?CofStock $stock
         */
        $stock = $stockRepository->find($id);
        if (!$stock)
            throw $this->createNotFoundException('The stock does not exist');
        $stock->setStock($stock->getStock() + 1);
        $stockRepository->add($stock, true);
        return $this->render('htmx/stock/show.html.twig', [
            'stock' => $stock,
        ]);
    }

    /**
     * @Route("htmx/stock/update/{id}", name="htmx_stock_update")
     * 
     * @param CofSupplier $supplier
     * @return Response
     */
    public function htmx_stock_update(
        int $id,
        Request $request,
        CofProductRepository $productRepository,
        CofSupplierRepository $supplierRepository,
        CofStockRepository $stockRepository
    ): Response {
        /**
         * @var ?CofStock $stock
         */
        $stock = $stockRepository->find($id);
        if (!$stock)
            throw $this->createNotFoundException('The stock does not exist');
        $stock->setStock($request->request->get('stock'));
        if ($stock->getStock() <= 0)
            $stockRepository->remove($stock, true);
        else
            $stockRepository->add($stock, true);
        return $this->render('htmx/stock/show.html.twig', [
            'stock' => $stock,
        ]);
    }
}
