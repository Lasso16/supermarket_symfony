<?php

namespace App\Repository;

use App\Entity\Producto;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Producto>
 */
class ProductoRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Producto::class);
    }

    /**
     * Find products by category
     * @return Producto[]
     */
    public function findByCategoria(int $categoriaId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.categoria = :categoriaId')
            ->setParameter('categoriaId', $categoriaId)
            ->orderBy('p.fechaCreacion', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find products by user (seller)
     * @return Producto[]
     */
    public function findByUsuario(int $usuarioId): array
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.usuario = :usuarioId')
            ->setParameter('usuarioId', $usuarioId)
            ->orderBy('p.fechaCreacion', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find latest products
     * @return Producto[]
     */
    public function findLatest(int $limit = 10): array
    {
        return $this->createQueryBuilder('p')
            ->orderBy('p.fechaCreacion', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    public function remove(Producto $entity, bool $flush = false): void
    {
        $this->getEntityManager()->remove($entity);

        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }

    public function findAllFiltered(array $filters): array
    {
        $qb = $this->createQueryBuilder('p');

        if (!empty($filters['categoria'])) {
            $qb->andWhere('p.categoria = :categoria')
                ->setParameter('categoria', $filters['categoria']);
        }

        // Date range filter: show products that overlap with the selected date range
        // A product overlaps if: product.fechaInicial <= filter.fecha_hasta AND product.fechaFinal >= filter.fecha_desde
        if (!empty($filters['fecha_desde']) || !empty($filters['fecha_hasta'])) {
            if (!empty($filters['fecha_desde'])) {
                $fechaDesde = new \DateTime($filters['fecha_desde']);
                $qb->andWhere('p.fechaFinal >= :desde')
                    ->setParameter('desde', $fechaDesde);
            }

            if (!empty($filters['fecha_hasta'])) {
                $fechaHasta = new \DateTime($filters['fecha_hasta']);
                $fechaHasta->setTime(23, 59, 59); // Include the entire day
                $qb->andWhere('p.fechaInicial <= :hasta')
                    ->setParameter('hasta', $fechaHasta);
            }
        }

        if (!empty($filters['usuario'])) {
            $qb->andWhere('p.usuario = :usuario')
                ->setParameter('usuario', $filters['usuario']);
        }

        if (!empty($filters['search'])) {
            $qb->andWhere('p.titulo LIKE :search OR p.descripcion LIKE :search')
                ->setParameter('search', '%' . $filters['search'] . '%');
        }

        return $qb->orderBy('p.fechaCreacion', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
