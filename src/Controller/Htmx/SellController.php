<?php

namespace App\Controller\Htmx;

use App\Entity\Sell;
use App\Entity\Supplier;
use App\Repository\SellRepository;
use App\Repository\StockRepository;
use App\Repository\ProductRepository;
use App\Repository\SupplierRepository;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class SellController extends AbstractController
{
    /**
     * @Route("htmx/{id}/sell/new", name="htmx_modal_sell_new")
     * 
     * @param Supplier $supplier
     * @return Response
     */
    public function htmx_modal_sell_new(
        int $id,
        Request $request,
        ProductRepository $productRepository,
        SupplierRepository $supplierRepository,
        StockRepository $stockRepository,
        SellRepository $sellRepository
    ): Response {
        if ($request->isMethod('POST')) {
            $errors = [];
            if (!$request->request->get('productId')) {
                $errors[] = 'Le produit est obligatoire';
            }
            if (!$request->request->get('quantity')) {
                $errors[] = 'La quantité est obligatoire';
            }
            if (!$request->request->get('soldAt')) {
                $errors[] = 'La date de vente est obligatoire';
            }
            //get supplier and verify if soldAt is between startAt and endAt
            $supplier = $supplierRepository->find($id);
            if (!$supplier) {
                $errors[] = 'Le fournisseur n\'existe pas';
            }
            if ($supplier && $supplier->getStartAt() > new \DateTime($request->request->get('soldAt'))) {
                $errors[] = 'La date de vente est antérieure à la date de début de contrat';
            }
            if ($supplier && $supplier->getEndAt() < new \DateTime($request->request->get('soldAt'))) {
                $errors[] = 'La date de vente est ultérieure à la date de fin de contrat';
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
                return $this->render('htmx/modal/new_sell.html.twig', [
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
            return $this->render('htmx/modal/new_sell_accepted.html.twig', [
                'sell' => $sell,
                'supplier' => $supplierRepository->find($id),
            ]);
        }
        return $this->render('htmx/modal/new_sell.html.twig', [
            'products' => $productRepository->findLikeName('', $id),
            'supplier' => $supplierRepository->find($id),
            'errors' => [],
        ]);
    }

    /**
     * @Route("htmx/{id}/sell/load", name="htmx_load_sells")
     * 
     * @param Supplier $supplier
     * @return Response
     */
    public function htmx_reload_sells(
        int $id,
        Request $request,
        ProductRepository $productRepository,
        SupplierRepository $supplierRepository,
        SellRepository $sellRepository,
        PaginatorInterface $paginator
    ): Response {
        /**
         * @var ?Supplier $supplier
         */
        $page = $request->query->get('page', 1);
        dump($request);
        $sort = $request->query->get('sort', 's.soldAt');
        $direction = $request->query->get('direction', 'desc');
        $month = $request->query->get('month', null);
        $supplier = $supplierRepository->find($id);
        $sellsPerMonth = $sellRepository->sellsPerMonthBySupplier($supplier);
        dump($sellsPerMonth);
        if (!$supplier)
            throw $this->createNotFoundException('The supplier does not exist');
        $query = $sellRepository->findBySupplierQuery($supplier, $sort, $direction, $month);
        $sells = $paginator->paginate($query, $page, 10);
        return $this->render('htmx/sell/list.html.twig', [
            'supplier' => $supplier,
            'sells' => $sells,
            'sort' => $sort,
            'direction' => $direction,
            'month' => $month,
            'sellsPerMonth' => $sellsPerMonth,
        ]);
    }
    /**
     * @Route("htmx/sell/{id}/delete", name="htmx_sell_delete")
     * 
     * @param Sell $sell
     * @return Response
     */
    public function htmx_sell_delete(
        int $id,
        ProductRepository $productRepository,
        SupplierRepository $supplierRepository,
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
