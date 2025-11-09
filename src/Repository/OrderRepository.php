<?php

namespace App\Repository;

use App\Entity\Order;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

class OrderRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        // This correctly sets $this->_em
        parent::__construct($registry, Order::class);
    }

    public function save(Order $order, bool $flush = true): void
    {
        $em = $this->getEntityManager(); // ✅ use getEntityManager() instead of $_em
        $em->persist($order);
        if ($flush) {
            $em->flush();
        }
    }

    public function remove(Order $order, bool $flush = true): void
    {
        $em = $this->getEntityManager(); // ✅ use getEntityManager() instead of $_em
        $em->remove($order);
        if ($flush) {
            $em->flush();
        }
    }
}
