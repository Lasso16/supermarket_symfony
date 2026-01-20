<?php

namespace App\Repository;

use App\Entity\Compra;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Compra>
 */
class CompraRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Compra::class);
    }

    public function findByUsuario(int $usuarioId): array
    {
        return $this->createQueryBuilder('c')
            ->andWhere('c.usuario = :usuarioId')
            ->setParameter('usuarioId', $usuarioId)
            ->orderBy('c.fechaCompra', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
