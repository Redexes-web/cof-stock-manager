<?php

namespace App\Entity;

use App\Repository\CofSupplierRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=CofSupplierRepository::class)
 */
class CofSupplier
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
     * @ORM\OneToMany(targetEntity=CofStock::class, mappedBy="supplier", orphanRemoval=true)
     */
    private $stocks;

    public function __construct()
    {
        $this->stocks = new ArrayCollection();
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
     * @return Collection<int, CofStock>
     */
    public function getStocks(): Collection
    {
        return $this->stocks;
    }

    public function addStock(CofStock $stock): self
    {
        if (!$this->stocks->contains($stock)) {
            $this->stocks[] = $stock;
            $stock->setSupplier($this);
        }

        return $this;
    }

    public function removeStock(CofStock $stock): self
    {
        if ($this->stocks->removeElement($stock)) {
            // set the owning side to null (unless already changed)
            if ($stock->getSupplier() === $this) {
                $stock->setSupplier(null);
            }
        }

        return $this;
    }
}
