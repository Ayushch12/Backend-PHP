<?php

namespace App\Repository;

use App\Entity\Product;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Product>
 */
class ProductRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Product::class);
    }

    /**
     *
     *
     * @param string $search
     * @param int|null $categoryId
     * @return Product[]
     */
    public function findBySearchAndCategory(string $search = '', ?int $categoryId = null): array
    {
        $qb = $this->createQueryBuilder('p');

        if (!empty($search)) {
            $qb->andWhere('p.name LIKE :search')
                ->setParameter('search', '%' . $search . '%');
        }

        if ($categoryId) {
            $qb->andWhere('p.category = :categoryId')
                ->setParameter('categoryId', $categoryId);
        }

        return $qb->getQuery()->getResult();
    }
}
