<?php

namespace App\Controller;

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

class HtmxController extends AbstractController
{
    /**
     * @Route("htmx/htmx_supplier_choices", name="htmx_supplier_choices")
     * 
     * @param CofSupplier $supplier
     * @return Response
     */
    public function htmx_product_choices(Request $request, CofProductRepository $productRepository, CofSupplierRepository $supplierRepository): Response
    {
        $products = $productRepository->findLikeName($request->get('name'), $request->get('supplierId'));
        return $this->render('htmx/htmx_product_choices.html.twig', [
            'products' => $products,
        ]);
    }
    /**
     * @Route("htmx/htmx_supplier_add_product/{id}", name="htmx_supplier_add_product")
     * 
     * @param CofSupplier $supplier
     * @return Response
     */
    public function htmx_supplier_add_product(
        int $id,
        Request $request,
        CofProductRepository $productRepository,
        CofSupplierRepository $supplierRepository,
        CofStockRepository $stockRepository
    ): Response {
        /**
         * @var ?CofSupplier $supplier
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
            $product = new CofProduct();
            $product->setName($request->request->get('name'));
            $product->setPrice($request->request->get('price'));
            $productRepository->add($product, true);
        }
        $stock = $stockRepository->findOneBy(['product' => $product, 'supplier' => $supplier]);
        if ($stock) {
            $stock->setStock($stock->getStock() + $quantity);
        } else {
            $stock = new CofStock();
            $stock->setProduct($product);
            $stock->setSupplier($supplier);
            $stock->setStock($quantity);
        }
        $stockRepository->add($stock, true);
        // $supplierRepository->add($supplier, true);
        return $this->render('htmx/htmx_supplier_add_product.html.twig', [
            'supplier' => $supplier,
            'stocks' => $supplier->getStocks(),
        ]);
    }

    /**
     * @Route("htmx/htmx_reload_products/{id}", name="htmx_reload_products")
     * 
     * @param CofSupplier $supplier
     * @return Response
     */
    public function htmx_reload_products(
        int $id,
        CofProductRepository $productRepository,
        CofSupplierRepository $supplierRepository
    ): Response {
        /**
         * @var ?CofSupplier $supplier
         */
        $supplier = $supplierRepository->find($id);
        if (!$supplier)
            throw $this->createNotFoundException('The supplier does not exist');
        return $this->render('htmx/htmx_supplier_add_product.html.twig', [
            'supplier' => $supplier,
            'stocks' => $supplier->getStocks(),
        ]);
    }

    /**
     * @Route("htmx/htmx_stock_decrease/{id}", name="htmx_stock_decrease")
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
        return $this->render('htmx/htmx_stock.html.twig', [
            'stock' => $stock,
        ]);
    }

    /**
     * @Route("htmx/htmx_stock_increase/{id}", name="htmx_stock_increase")
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
        return $this->render('htmx/htmx_stock.html.twig', [
            'stock' => $stock,
        ]);
    }

    /**
     * @Route("htmx/htmx_stock_update/{id}", name="htmx_stock_update")
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
        return $this->render('htmx/htmx_stock.html.twig', [
            'stock' => $stock,
        ]);
    }

    /**
     * @Route("htmx/htmx_product_delete/{id}", name="htmx_product_delete")
     * 
     * @param CofSupplier $supplier
     * @return Response
     */
    public function htmx_product_delete(
        int $id,
        CofProductRepository $productRepository,
        CofSupplierRepository $supplierRepository,
        CofStockRepository $stockRepository
    ): Response {
        /**
         * @var ?CofProduct $product
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
            return $this->render('htmx/product_row.html.twig', [
                'message' => 'Le produit existe dans les stocks des boutiques suivantes: ' . implode(', ', $supplierNames),
                'product' => $product,
            ]);
        }

        $productRepository->remove($product, true);
        return $this->render('htmx/empty.html.twig');
    }

    /**
     * @Route("htmx/htmx_modal_new_sell/{id}", name="htmx_modal_new_sell")
     * 
     * @param CofSupplier $supplier
     * @return Response
     */
    public function htmx_modal_new_sell(
        int $id,
        Request $request,
        CofProductRepository $productRepository,
        CofSupplierRepository $supplierRepository,
        CofStockRepository $stockRepository,
        SellRepository $sellRepository
    ): Response {
        if ($request->isMethod('POST')) {
            // check if $request->request->get('productId') exist and is a valid product id
            // check if $request->request->get('quantity') is a valid number
            // check if $request->request->get('quantity') is less than or equal to the stock
            // check if soldAt is a valid date
            $errors = [];
            dump($request->request->all());
            if (!$request->request->get('productId')) {
                $errors[] = 'Le produit est obligatoire';
            }
            if (!$request->request->get('quantity')) {
                $errors[] = 'La quantité est obligatoire';
            }
            if (!$request->request->get('soldAt')) {
                $errors[] = 'La date de vente est obligatoire';
            }
            $product = $productRepository->find($request->request->get('productId'));
            if (!$product) {
                $errors[] = 'Le produit n\'existe pas';
            }
            $stock = $stockRepository->findOneBy(['product' => $product, 'supplier' => $supplierRepository->find($id)]);
            if (!$stock) {
                $errors[] = 'Le produit n\'existe pas dans le stock de cette boutique';
            }
            if ($stock && $stock->getStock() < $request->request->get('quantity')) {
                $errors[] = 'La quantité est supérieure au stock disponible dans cette boutique';
            }
            if ($errors) {
                return $this->render('htmx/htmx_modal_new_sell.html.twig', [
                    'products' => $productRepository->findAll(),
                    'supplier' => $supplierRepository->find($id),
                    'errors' => $errors,
                ]);
            }
            $sell = new Sell();
            $sell->setProduct($product);
            $sell->setSupplier($supplierRepository->find($id));
            $sell->setQuantity($request->request->get('quantity'));
            $sell->setSoldAt(new \DateTime($request->request->get('soldAt')));
            $stock->setStock($stock->getStock() - $request->request->get('quantity'));
            $stockRepository->add($stock, true);
            $sellRepository->add($sell, true);
            return $this->render('htmx/htmx_modal_new_sell_accepted.html.twig', [
                'sell' => $sell,
                'supplier' => $supplierRepository->find($id),
            ]);
        }
        return $this->render('htmx/htmx_modal_new_sell.html.twig', [
            'products' => $productRepository->findLikeName('', $id),
            'supplier' => $supplierRepository->find($id),
            'errors' => [],
        ]);
    }

    /**
     * @Route("htmx/htmx_reload_sells/{id}", name="htmx_reload_sells")
     * 
     * @param CofSupplier $supplier
     * @return Response
     */
    public function htmx_reload_sells(
        int $id,
        CofProductRepository $productRepository,
        CofSupplierRepository $supplierRepository,
        SellRepository $sellRepository
    ): Response {
        /**
         * @var ?CofSupplier $supplier
         */
        $supplier = $supplierRepository->find($id);
        if (!$supplier)
            throw $this->createNotFoundException('The supplier does not exist');
        return $this->render('htmx/htmx_supplier_add_sell.html.twig', [
            'supplier' => $supplier,
            'sells' => $sellRepository->findBy(['supplier' => $supplier], ['soldAt' => 'DESC']),
        ]);
    }
    /**
     * @Route("htmx/htmx_sell_delete/{id}", name="htmx_sell_delete")
     * 
     * @param CofSupplier $supplier
     * @return Response
     */
    public function htmx_sell_delete(
        int $id,
        CofProductRepository $productRepository,
        CofSupplierRepository $supplierRepository,
        SellRepository $sellRepository
    ): Response {
        /**
         * @var ?Sell $sell
         */
        $sell = $sellRepository->find($id);
        if (!$sell)
            throw $this->createNotFoundException('The sell does not exist');
        $sellRepository->remove($sell, true);
        return $this->render('htmx/empty.html.twig');
    }
}
