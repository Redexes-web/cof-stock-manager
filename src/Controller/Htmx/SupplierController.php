<?php

namespace App\Controller\Htmx;

use App\Entity\CofSupplier;
use App\Repository\CofSupplierRepository;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\String\Slugger\SluggerInterface;

class SupplierController extends AbstractController
{

    /**
     * @Route("htmx/supplier/new", name="htmx_supplier_new")
     * 
     * @param CofSupplier $supplier
     * @return Response
     */
    public function htmx_supplier_new(
        Request $request,
        CofSupplierRepository $supplierRepository,
        SluggerInterface $slugger
    ): Response {
        if ($request->isMethod('POST')) {
            $supplier = new CofSupplier();
            $supplier->setName($request->request->get('name'));
            $slug = $slugger->slug($supplier->getName());
            $slugExist = $supplierRepository->findOneBy(['slug' => $slug]);
            $supplier->setSlug($slugger->slug(($supplier->getName() . ($slugExist ? $slugExist->getId() : ''))));
            $supplier->setStartAt(new \DateTime($request->request->get('startAt')));
            $supplier->setEndAt(new \DateTime($request->request->get('endAt')));
            $supplier->setRentPrice($request->request->get('rentPrice'));
            $supplier->setCommissionPercentage($request->request->get('commissionPercentage'));
            $supplierRepository->add($supplier, true);
            return $this->render('htmx/supplier/new_accepted.html.twig', [
                'supplier' => $supplier,
            ]);
        }
        return $this->render('htmx/supplier/new.html.twig', [
            'errors' => [],
        ]);
    }

    /**
     * @Route("htmx/supplier/{id}/delete", name="htmx_supplier_delete")
     * 
     * @param CofSupplier $supplier
     * @return Response
     */
    public function htmx_supplier_delete(
        int $id,
        CofSupplierRepository $supplierRepository
    ): Response {
        /**
         * @var ?CofSupplier $supplier
         */
        $supplier = $supplierRepository->find($id);
        $errors = [];
        if (!$supplier)
            throw $this->createNotFoundException('The supplier does not exist');
        if ($supplier->getSells()->count() > 0)
            $errors[] = 'Le fournisseur a des ventes enregistrées';

        if (!empty($errors)) {
            return $this->render('htmx/supplier/detail.html.twig', [
                'errors' => $errors,
                'supplier' => $supplier,
            ]);
        }
        $supplierRepository->remove($supplier, true);
        return $this->render('htmx/empty.html.twig');
    }

    /**
     * @Route("htmx/supplier/{id}/edit", name="htmx_supplier_edit")
     * 
     * @param CofSupplier $supplier
     * @return Response
     */
    public function htmx_supplier_edit(
        int $id,
        Request $request,
        CofSupplierRepository $supplierRepository,
        SluggerInterface $slugger
    ): Response {
        /**
         * @var ?CofSupplier $supplier
         */
        $supplier = $supplierRepository->find($id);
        if ($request->isMethod('POST')) {
            $supplier->setName($request->request->get('name'));
            $slug = $slugger->slug($supplier->getName());
            $slugExist = $supplierRepository->findOneBy(['slug' => $slug]);
            $slugExist = $slugExist && $slugExist->getId() !== $supplier->getId() ? $slugExist : null;
            $supplier->setSlug($slugger->slug($supplier->getName() . ($slugExist != null ? $slugExist->getId() : '')));
            $supplier->setStartAt(new \DateTime($request->request->get('startAt')));
            $supplier->setEndAt(new \DateTime($request->request->get('endAt')));
            $supplier->setRentPrice($request->request->get('rentPrice'));
            $supplier->setCommissionPercentage($request->request->get('commissionPercentage'));
            $supplierRepository->add($supplier, true);
            return $this->render('htmx/supplier/new_accepted.html.twig', [
                'supplier' => $supplier,
                'isEdit' => true,
            ]);
        }
        return $this->render('htmx/supplier/new.html.twig', [
            'supplier' => $supplier,
            'errors' => [],
        ]);
    }
    /**
     * @Route("htmx/supplier/{id}/load-details", name="htmx_supplier_load_details")
     * 
     * @param CofSupplier $supplier
     * @return Response
     */
    public function htmx_supplier_load(
        int $id,
        CofSupplierRepository $supplierRepository
    ): Response {
        /**
         * @var ?CofSupplier $supplier
         */
        $supplier = $supplierRepository->find($id);
        if (!$supplier)
            throw $this->createNotFoundException('The supplier does not exist');
        return $this->render('htmx/supplier/detail.html.twig', [
            'supplier' => $supplier,
        ]);
    }

    /**
     * @Route("htmx/suppliers-load", name="htmx_load_suppliers")
     * 
     * @param CofSupplier $supplier
     * @return Response
     */
    public function htmx_reload_suppliers(
        CofSupplierRepository $supplierRepository
    ): Response {
        return $this->render('htmx/supplier/list.html.twig', [
            'suppliers' => $supplierRepository->findAll(),
        ]);
    }
}
