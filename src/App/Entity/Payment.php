<?php

namespace App\Entity;

/**
 * Payment
 */
class Payment
{
    /**
     * @var int
     */
    private $id;

    /**
     * @var string
     */
    private $montant;

    /**
     * @var string
     */
    private $modePaiement;

    /**
     * @var datetime_immutable
     */
    private $datePaiement;

    /**
     * @var string
     */
    private $statut;

    /**
     * @var \App\Entity\Order
     */
    private $order;


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
     * Set montant.
     *
     * @param string $montant
     *
     * @return Payment
     */
    public function setMontant($montant)
    {
        $this->montant = $montant;

        return $this;
    }

    /**
     * Get montant.
     *
     * @return string
     */
    public function getMontant()
    {
        return $this->montant;
    }

    /**
     * Set modePaiement.
     *
     * @param string $modePaiement
     *
     * @return Payment
     */
    public function setModePaiement($modePaiement)
    {
        $this->modePaiement = $modePaiement;

        return $this;
    }

    /**
     * Get modePaiement.
     *
     * @return string
     */
    public function getModePaiement()
    {
        return $this->modePaiement;
    }

    /**
     * Set datePaiement.
     *
     * @param datetime_immutable $datePaiement
     *
     * @return Payment
     */
    public function setDatePaiement($datePaiement)
    {
        $this->datePaiement = $datePaiement;

        return $this;
    }

    /**
     * Get datePaiement.
     *
     * @return datetime_immutable
     */
    public function getDatePaiement()
    {
        return $this->datePaiement;
    }

    /**
     * Set statut.
     *
     * @param string $statut
     *
     * @return Payment
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
     * Set order.
     *
     * @param \App\Entity\Order $order
     *
     * @return Payment
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
}
