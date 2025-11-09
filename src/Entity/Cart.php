<?php

namespace App\Entity;

use App\Repository\CartRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;

#[ORM\Entity(repositoryClass: CartRepository::class)]
class Cart
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 20)]
    private ?string $cardNumber = null;

    #[ORM\Column(length: 50)]
    private ?string $cardType = null;

    #[ORM\Column(type: 'date')]
    private ?\DateTimeInterface $expirationDate = null;

    #[ORM\ManyToOne(inversedBy: 'carts')]
    #[ORM\JoinColumn(nullable: false)]
    private ?User $user = null;

    #[ORM\OneToMany(mappedBy: 'cart', targetEntity: Order::class)]
    private Collection $orders;

    public function __construct() { $this->orders = new ArrayCollection(); }

    public function getId(): ?int { return $this->id; }

    public function getCardNumber(): ?string { return $this->cardNumber; }
    public function setCardNumber(string $cardNumber): static { $this->cardNumber = $cardNumber; return $this; }

    public function getCardType(): ?string { return $this->cardType; }
    public function setCardType(string $cardType): static { $this->cardType = $cardType; return $this; }

    public function getExpirationDate(): ?\DateTimeInterface { return $this->expirationDate; }
    public function setExpirationDate(\DateTimeInterface $expirationDate): static { $this->expirationDate = $expirationDate; return $this; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(?User $user): static { $this->user = $user; return $this; }

    public function getOrders(): Collection { return $this->orders; }
    public function addOrder(Order $order): static {
        if (!$this->orders->contains($order)) {
            $this->orders->add($order);
            $order->setCart($this);
        }
        return $this;
    }
    public function removeOrder(Order $order): static {
        if ($this->orders->removeElement($order) && $order->getCart() === $this) {
            $order->setCart(null);
        }
        return $this;
    }
}
