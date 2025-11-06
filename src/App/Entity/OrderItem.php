<?php

namespace App\Entity;

/**
 * OrderItem
 */
class OrderItem
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var int
     */
    private $quantite;

    /**
     * @var string
     */
    private $prixUnitaire;

    /**
     * @var \App\Entity\Order
     */
    private $order;

    /**
     * @var \App\Entity\Product
     */
    private $product;


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
     * Set quantite.
     *
     * @param int $quantite
     *
     * @return OrderItem
     */
    public function setQuantite($quantite)
    {
        $this->quantite = $quantite;

        return $this;
    }

    /**
     * Get quantite.
     *
     * @return int
     */
    public function getQuantite()
    {
        return $this->quantite;
    }

    /**
     * Set prixUnitaire.
     *
     * @param string $prixUnitaire
     *
     * @return OrderItem
     */
    public function setPrixUnitaire($prixUnitaire)
    {
        $this->prixUnitaire = $prixUnitaire;

        return $this;
    }

    /**
     * Get prixUnitaire.
     *
     * @return string
     */
    public function getPrixUnitaire()
    {
        return $this->prixUnitaire;
    }

    /**
     * Set order.
     *
     * @param \App\Entity\Order $order
     *
     * @return OrderItem
     */
    public function setOrder(\App\Entity\Order $order)
    {
        $this->order = $order;

        return $this;
    }

    /**
     * Get order.
     *
     * @return \App\Entity\Order
     */
    public function getOrder()
    {
        return $this->order;
    }

    /**
     * Set product.
     *
     * @param \App\Entity\Product $product
     *
     * @return OrderItem
     */
    public function setProduct(\App\Entity\Product $product)
    {
        $this->product = $product;

        return $this;
    }

    /**
     * Get product.
     *
     * @return \App\Entity\Product
     */
    public function getProduct()
    {
        return $this->product;
    }
}
