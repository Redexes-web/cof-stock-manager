<?php

namespace App\Entity;

use App\Repository\SellRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SellRepository::class)
 */
class Sell
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="datetime")
     */
    private $soldAt;

    /**
     * @ORM\ManyToOne(targetEntity=CofProduct::class, inversedBy="sells")
     * @ORM\JoinColumn(nullable=false)
     */
    private $product;

    /**
     * @ORM\ManyToOne(targetEntity=CofSupplier::class, inversedBy="sells")
     * @ORM\JoinColumn(nullable=false)
     */
    private $supplier;

    /**
     * @ORM\Column(type="integer")
     */
    private $quantity;

    private $total;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getSoldAt(): ?\DateTimeInterface
    {
        return $this->soldAt;
    }

    public function setSoldAt(\DateTimeInterface $soldAt): self
    {
        $this->soldAt = $soldAt;

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

    public function getQuantity(): ?int
    {
        return $this->quantity;
    }

    public function setQuantity(int $quantity): self
    {
        $this->quantity = $quantity;

        return $this;
    }
}
