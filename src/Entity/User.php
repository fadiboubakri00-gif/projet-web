<?php

namespace App\Entity;

use App\Repository\UserRepository;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\ORM\Mapping as ORM;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;
use Symfony\Component\Security\Core\User\UserInterface;

#[ORM\Entity(repositoryClass: UserRepository::class)]
#[ORM\Table(name: '`user`')]
class User implements UserInterface, PasswordAuthenticatedUserInterface
{
    #[ORM\Id, ORM\GeneratedValue, ORM\Column]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    private ?string $username = null;

    #[ORM\Column(length: 255)]
    private ?string $email = null;

    #[ORM\Column(length: 255)]
    private ?string $password = null;

    #[ORM\OneToOne(mappedBy: 'owner', targetEntity: Cart::class, cascade: ['persist', 'remove'])]
    private ?Cart $cart = null;

    #[ORM\OneToMany(targetEntity: Order::class, mappedBy: 'orderUser')]
    private Collection $orders;

    public function __construct()
    {
        $this->orders = new ArrayCollection();
    }

    public function getId(): ?int { return $this->id; }
    public function getUsername(): ?string { return $this->username; }
    public function setUsername(string $username): static { $this->username = $username; return $this; }

    public function getEmail(): ?string { return $this->email; }
    public function setEmail(string $email): static { $this->email = $email; return $this; }

    public function getPassword(): ?string { return $this->password; }
    public function setPassword(string $password): static { $this->password = $password; return $this; }

    public function getCart(): ?Cart { return $this->cart; }
    public function setCart(?Cart $cart): static {
        if ($cart !== null && $cart->getOwner() !== $this) { $cart->setOwner($this); }
        $this->cart = $cart;
        return $this;
    }

    public function getOrders(): Collection { return $this->orders; }
    public function addOrder(Order $order): static {
        if (!$this->orders->contains($order)) { $this->orders->add($order); $order->setOrderUser($this); }
        return $this;
    }
    public function removeOrder(Order $order): static {
        if ($this->orders->removeElement($order)) { if ($order->getOrderUser() === $this) { $order->setOrderUser(null); } }
        return $this;
    }

    // ---------------- Security ----------------

    public function getRoles(): array { return ['ROLE_USER']; }
    public function getUserIdentifier(): string { return (string)$this->email; }
    public function eraseCredentials(): void {}
}
