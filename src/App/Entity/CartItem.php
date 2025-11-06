<?php

namespace App\Entity;

/**
 * CartItem
 */
class CartItem
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
    private $sousTotal;

    /**
     * @var \App\Entity\Cart
     */
    private $cart;

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
     * @return CartItem
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
     * Set sousTotal.
     *
     * @param string $sousTotal
     *
     * @return CartItem
     */
    public function setSousTotal($sousTotal)
    {
        $this->sousTotal = $sousTotal;

        return $this;
    }

    /**
     * Get sousTotal.
     *
     * @return string
     */
    public function getSousTotal()
    {
        return $this->sousTotal;
    }

    /**
     * Set cart.
     *
     * @param \App\Entity\Cart $cart
     *
     * @return CartItem
     */
    public function setCart(\App\Entity\Cart $cart)
    {
        $this->cart = $cart;

        return $this;
    }

    /**
     * Get cart.
     *
     * @return \App\Entity\Cart
     */
    public function getCart()
    {
        return $this->cart;
    }

    /**
     * Set product.
     *
     * @param \App\Entity\Product $product
     *
     * @return CartItem
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
