<?php

namespace App\Entity;

use App\Repository\OrderRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: OrderRepository::class)]
#[ORM\Table(name: '`order`')]
class Order
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'orders')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $orderUser = null;

    /**
     * @var Collection<int, Product>
     */
    #[ORM\ManyToMany(targetEntity: Product::class, inversedBy: 'orders')]
    #[ORM\JoinTable(name: 'order_product')]
    private Collection $products;



    public function __construct()
    {
        $this->products = new ArrayCollection();
        
    }

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getOrderUser(): ?User
    {
        return $this->orderUser;
    }

    public function setOrderUser(?User $orderUser): static
    {
        $this->orderUser = $orderUser;

        return $this;
    }

    /**
     * @return Collection<int, Product>
     */
    public function getProducts(): Collection
    {
        return $this->products;
    }

    public function addProduct(Product $product): static
    {
        if (!$this->products->contains($product)) {
            $this->products->add($product);
        }

        return $this;
    }

    public function removeProduct(Product $product): static
    {
        $this->products->removeElement($product);

        return $this;
    }

    

    /**
     * Calculate total automatically from products
     */
    public function getTotal(): float
    {
        $total = 0.0;
        foreach ($this->products as $product) {
            $total += $product->getPrice();
        }
        return $total;
    }

    /**
     * Get number of items in order
     */
    public function getItemsCount(): int
    {
        return $this->products->count();
    }
}