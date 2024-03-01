<?php

namespace App\Entity;

use App\Repository\CofStockRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CofStockRepository::class)
 */
class CofStock
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="integer")
     */
    private $stock;

    /**
     * @ORM\ManyToOne(targetEntity=CofProduct::class, inversedBy="stocks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity=CofSupplier::class, inversedBy="stocks")
     * @ORM\JoinColumn(nullable=false)
     */
    private $supplier;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getStock(): ?int
    {
        return $this->stock;
    }

    public function setStock(int $stock): self
    {
        $this->stock = $stock;

        return $this;
    }

    public function getProduct(): ?CofProduct
    {
        return $this->product;
    }

    public function setProduct(?CofProduct $product): self
    {
        $this->product = $product;

        return $this;
    }

    public function getSupplier(): ?CofSupplier
    {
        return $this->supplier;
    }

    public function setSupplier(?CofSupplier $supplier): self
    {
        $this->supplier = $supplier;

        return $this;
    }
}
