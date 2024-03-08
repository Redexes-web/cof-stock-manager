<?php

namespace App\Controller\Htmx;

use App\Entity\Sell;
use App\Entity\Stock;
use App\Entity\Product;
use App\Entity\Supplier;
use App\Repository\SellRepository;
use App\Repository\StockRepository;
use App\Repository\ProductRepository;
use App\Repository\SupplierRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProductController extends AbstractController
{
    /**
     * @Route("htmx/product/supplier_choices", name="htmx_supplier_choices")
     * 
     * @param Supplier $supplier
     * @return Response
     */
    public function htmx_product_choices(
        Request $request,
        ProductRepository $productRepository,
        SupplierRepository $supplierRepository
    ): Response {
        $products = $productRepository->findLikeName($request->get('name'), $request->get('supplierId'));
        return $this->render('htmx/product/choices.html.twig', [
            'products' => $products,
        ]);
    }
    /**
     * @Route("htmx/{id}/product/new", name="htmx_supplier_product_new")
     * 
     * @param Supplier $supplier
     * @return Response
     */
    public function htmx_supplier_product_new(
        int $id,
        Request $request,
        ProductRepository $productRepository,
        SupplierRepository $supplierRepository,
        StockRepository $stockRepository
    ): Response {
        /**
         * @var ?Supplier $supplier
         */
        $supplier = $supplierRepository->find($id);
        $name = $request->request->get('name');
        $price = $request->request->get('price');
        $quantity = $request->request->get('stock', 1);
        if (!$name || !$price)
            throw $this->createNotFoundException('The product price or name is missing');
        if (!$supplier)
            throw $this->createNotFoundException('The supplier does not exist');
        $product = $productRepository->findOneBy(['name' => $request->request->get('name'), 'price' => $request->request->get('price')]);
        if (!$product) {
            $product = new Product();
            $product->setName($request->request->get('name'));
            $product->setPrice($request->request->get('price'));
            $productRepository->add($product, true);
        }
        $stock = $stockRepository->findOneBy(['product' => $product, 'supplier' => $supplier]);
        if ($stock) {
            $stock->setStock($stock->getStock() + $quantity);
        } else {
            $stock = new Stock();
            $stock->setProduct($product);
            $stock->setSupplier($supplier);
            $stock->setStock($quantity);
        }
        $stockRepository->add($stock, true);
        // $supplierRepository->add($supplier, true);
        return $this->render('htmx/product/list.html.twig', [
            'supplier' => $supplier,
            'stocks' => $supplier->getStocks(),
        ]);
    }

    /**
     * @Route("htmx/{id}/product/load", name="htmx_load_products")
     * 
     * @param Supplier $supplier
     * @return Response
     */
    public function htmx_reload_products(
        int $id,
        ProductRepository $productRepository,
        SupplierRepository $supplierRepository
    ): Response {
        /**
         * @var ?Supplier $supplier
         */
        $supplier = $supplierRepository->find($id);
        if (!$supplier)
            throw $this->createNotFoundException('The supplier does not exist');
        return $this->render('htmx/product/list.html.twig', [
            'supplier' => $supplier,
            'stocks' => $supplier->getStocks(),
        ]);
    }

    /**
     * @Route("htmx/htmx_product_delete/{id}", name="htmx_product_delete")
     * 
     * @param Supplier $supplier
     * @return Response
     */
    public function htmx_product_delete(
        int $id,
        ProductRepository $productRepository,
        SupplierRepository $supplierRepository,
        StockRepository $stockRepository
    ): Response {
        /**
         * @var ?Product $product
         */
        $product = $productRepository->find($id);
        if (!$product)
            throw $this->createNotFoundException('The product does not exist');
        if ($product->getStocks()->count() > 0) {

            //send a 418 status code
            $supplierNames = [];
            foreach ($product->getStocks() as $stock) {
                $supplierNames[] = $stock->getSupplier()->getName();
            }
            return $this->render('htmx/product/row.html.twig', [
                'message' => 'Le produit existe dans les stocks des boutiques suivantes: ' . implode(', ', $supplierNames),
                'product' => $product,
            ]);
        }
        if ($product->getSells()->count() > 0) {
            $supplierNames = [];
            foreach ($product->getSells() as $sell) {
                $supplierNames[] = $sell->getSupplier()->getName();
            }
            return $this->render('htmx/product/row.html.twig', [
                'message' => 'Le produit existe dans les ventes des boutiques suivantes: ' . implode(', ', $supplierNames),
                'product' => $product,
            ]);
        }

        $productRepository->remove($product, true);
        return $this->render('htmx/empty.html.twig');
    }
}
