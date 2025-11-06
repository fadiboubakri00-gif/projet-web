<?php

namespace App\Entity;

/**
 * Order
 */
class Order
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $total;

    /**
     * @var string
     */
    private $statut;

    /**
     * @var datetime_immutable
     */
    private $dateCommande;

    /**
     * @var \App\Entity\Payment
     */
    private $payment;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $orderItems;

    /**
     * @var \App\Entity\User
     */
    private $user;

    /**
     * @var \App\Entity\Address
     */
    private $adresse;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->orderItems = new \Doctrine\Common\Collections\ArrayCollection();
    }

    /**
     * Get id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Set total.
     *
     * @param string $total
     *
     * @return Order
     */
    public function setTotal($total)
    {
        $this->total = $total;

        return $this;
    }

    /**
     * Get total.
     *
     * @return string
     */
    public function getTotal()
    {
        return $this->total;
    }

    /**
     * Set statut.
     *
     * @param string $statut
     *
     * @return Order
     */
    public function setStatut($statut)
    {
        $this->statut = $statut;

        return $this;
    }

    /**
     * Get statut.
     *
     * @return string
     */
    public function getStatut()
    {
        return $this->statut;
    }

    /**
     * Set dateCommande.
     *
     * @param datetime_immutable $dateCommande
     *
     * @return Order
     */
    public function setDateCommande($dateCommande)
    {
        $this->dateCommande = $dateCommande;

        return $this;
    }

    /**
     * Get dateCommande.
     *
     * @return datetime_immutable
     */
    public function getDateCommande()
    {
        return $this->dateCommande;
    }

    /**
     * Set payment.
     *
     * @param \App\Entity\Payment|null $payment
     *
     * @return Order
     */
    public function setPayment(\App\Entity\Payment $payment = null)
    {
        $this->payment = $payment;

        return $this;
    }

    /**
     * Get payment.
     *
     * @return \App\Entity\Payment|null
     */
    public function getPayment()
    {
        return $this->payment;
    }

    /**
     * Add orderItem.
     *
     * @param \App\Entity\OrderItem $orderItem
     *
     * @return Order
     */
    public function addOrderItem(\App\Entity\OrderItem $orderItem)
    {
        $this->orderItems[] = $orderItem;

        return $this;
    }

    /**
     * Remove orderItem.
     *
     * @param \App\Entity\OrderItem $orderItem
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeOrderItem(\App\Entity\OrderItem $orderItem)
    {
        return $this->orderItems->removeElement($orderItem);
    }

    /**
     * Get orderItems.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getOrderItems()
    {
        return $this->orderItems;
    }

    /**
     * Set user.
     *
     * @param \App\Entity\User $user
     *
     * @return Order
     */
    public function setUser(\App\Entity\User $user)
    {
        $this->user = $user;

        return $this;
    }

    /**
     * Get user.
     *
     * @return \App\Entity\User
     */
    public function getUser()
    {
        return $this->user;
    }

    /**
     * Set adresse.
     *
     * @param \App\Entity\Address $adresse
     *
     * @return Order
     */
    public function setAdresse(\App\Entity\Address $adresse)
    {
        $this->adresse = $adresse;

        return $this;
    }

    /**
     * Get adresse.
     *
     * @return \App\Entity\Address
     */
    public function getAdresse()
    {
        return $this->adresse;
    }
}
