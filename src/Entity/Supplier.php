<?php

namespace App\Entity;

use App\Repository\SupplierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=SupplierRepository::class)
 */
class Supplier
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string", length=550)
     */
    private $name;

    /**
     * @ORM\OneToMany(targetEntity=Stock::class, mappedBy="supplier", orphanRemoval=true)
     */
    private $stocks;

    /**
     * @ORM\OneToMany(targetEntity=Sell::class, mappedBy="supplier", orphanRemoval=true)
     */
    private $sells;

    /**
     * //default is the oldest date possible
     * @ORM\Column(type="datetime", options={"default": "1970-01-01 00:00:00"})
     */
    private $startAt;

    /**
     * @ORM\Column(type="datetime", options={"default": "9998-12-31 23:59:59"})
     */
    private $endAt;

    /**
     * @ORM\Column(type="float", options={"default": 0})
     */
    private $rentPrice;

    /**
     * @ORM\Column(type="float", options={"default": 0})
     */
    private $commissionPercentage;

    /**
     * @ORM\Column(type="string", length=600, unique=true, nullable=true)
     */
    private $slug;

    public function __construct()
    {
        $this->stocks = new ArrayCollection();
        $this->sells = new ArrayCollection();
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;

        return $this;
    }

    /**
     * @return Collection<int, Stock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStock(Stock $stock): self
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks[] = $stock;
            $stock->setSupplier($this);
        }

        return $this;
    }

    public function removeStock(Stock $stock): self
    {
        if ($this->stocks->removeElement($stock)) {
            // set the owning side to null (unless already changed)
            if ($stock->getSupplier() === $this) {
                $stock->setSupplier(null);
            }
        }

        return $this;
    }

    /**
     * @return Collection<int, Sell>
     */
    public function getSells(): Collection
    {
        return $this->sells;
    }

    public function addSell(Sell $sell): self
    {
        if (!$this->sells->contains($sell)) {
            $this->sells[] = $sell;
            $sell->setSupplier($this);
        }

        return $this;
    }

    public function removeSell(Sell $sell): self
    {
        if ($this->sells->removeElement($sell)) {
            // set the owning side to null (unless already changed)
            if ($sell->getSupplier() === $this) {
                $sell->setSupplier(null);
            }
        }

        return $this;
    }

    public function getStartAt(): ?\DateTimeInterface
    {
        return $this->startAt;
    }

    public function setStartAt(?\DateTimeInterface $startAt): self
    {
        $this->startAt = $startAt;

        return $this;
    }

    public function getEndAt(): ?\DateTimeInterface
    {
        return $this->endAt;
    }

    public function setEndAt(?\DateTimeInterface $endAt): self
    {
        $this->endAt = $endAt;

        return $this;
    }

    public function getRentPrice(): ?float
    {
        return $this->rentPrice;
    }

    public function setRentPrice(float $rentPrice): self
    {
        $this->rentPrice = $rentPrice;

        return $this;
    }

    public function getCommissionPercentage(): ?float
    {
        return $this->commissionPercentage;
    }

    public function setCommissionPercentage(float $commissionPercentage): self
    {
        $this->commissionPercentage = $commissionPercentage;

        return $this;
    }

    public function getSlug(): ?string
    {
        return $this->slug;
    }

    public function setSlug(string $slug): self
    {
        $this->slug = $slug;

        return $this;
    }
}
