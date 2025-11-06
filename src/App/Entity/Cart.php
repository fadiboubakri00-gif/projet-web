<?php

namespace App\Entity;

/**
 * Cart
 */
class Cart
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var datetime_immutable
     */
    private $dateCreation;

    /**
     * @var bool
     */
    private $actif;

    /**
     * @var \Doctrine\Common\Collections\Collection
     */
    private $cartItems;

    /**
     * @var \App\Entity\User
     */
    private $user;

    /**
     * Constructor
     */
    public function __construct()
    {
        $this->cartItems = new \Doctrine\Common\Collections\ArrayCollection();
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
     * Set dateCreation.
     *
     * @param datetime_immutable $dateCreation
     *
     * @return Cart
     */
    public function setDateCreation($dateCreation)
    {
        $this->dateCreation = $dateCreation;

        return $this;
    }

    /**
     * Get dateCreation.
     *
     * @return datetime_immutable
     */
    public function getDateCreation()
    {
        return $this->dateCreation;
    }

    /**
     * Set actif.
     *
     * @param bool $actif
     *
     * @return Cart
     */
    public function setActif($actif)
    {
        $this->actif = $actif;

        return $this;
    }

    /**
     * Get actif.
     *
     * @return bool
     */
    public function getActif()
    {
        return $this->actif;
    }

    /**
     * Add cartItem.
     *
     * @param \App\Entity\CartItem $cartItem
     *
     * @return Cart
     */
    public function addCartItem(\App\Entity\CartItem $cartItem)
    {
        $this->cartItems[] = $cartItem;

        return $this;
    }

    /**
     * Remove cartItem.
     *
     * @param \App\Entity\CartItem $cartItem
     *
     * @return boolean TRUE if this collection contained the specified element, FALSE otherwise.
     */
    public function removeCartItem(\App\Entity\CartItem $cartItem)
    {
        return $this->cartItems->removeElement($cartItem);
    }

    /**
     * Get cartItems.
     *
     * @return \Doctrine\Common\Collections\Collection
     */
    public function getCartItems()
    {
        return $this->cartItems;
    }

    /**
     * Set user.
     *
     * @param \App\Entity\User $user
     *
     * @return Cart
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
}
