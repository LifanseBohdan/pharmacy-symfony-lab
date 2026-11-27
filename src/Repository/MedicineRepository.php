<?php

namespace App\Repository;

use App\Entity\Medicine;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Medicine>
 */
class MedicineRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Medicine::class);
    }

    /**
     * @return Medicine[]
     */
    public function findAllWithCategory(?bool $inStock = null): array
    {
        $qb = $this->createQueryBuilder('m')
            ->leftJoin('m.category', 'c')
            ->addSelect('c')
            ->orderBy('m.id', 'ASC');

        if ($inStock === true) {
            $qb->andWhere('m.stockQuantity > 0');
        }

        return $qb->getQuery()->getResult();
    }
}
