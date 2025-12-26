<?php

namespace App\Repository;

use App\Entity\ProductReview;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\ORM\Tools\Pagination\Paginator;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<ProductReview>
 */
final class ProductReviewRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProductReview::class);
    }

    /**
     * @param array $data
     * @param int $itemsPerPage
     * @param int $page
     * @return array{items: mixed, totalPageCount: int, totalItems: int}
     */
    public function getAllByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $qb = $this->createQueryBuilder('e')
            ->leftJoin('e.product', 'p')->addSelect('p')
            ->leftJoin('e.author', 'a')->addSelect('a');

        if (isset($data['productId'])) {
            $qb->andWhere('p.id = :pid')
                ->setParameter('pid', (int) $data['productId']);
        }

        if (isset($data['authorId'])) {
            $qb->andWhere('a.id = :aid')
                ->setParameter('aid', (int) $data['authorId']);
        }

        if (isset($data['rating'])) {
            $qb->andWhere('e.rating = :rating')
                ->setParameter('rating', (int) $data['rating']);
        }

        $itemsPerPage = max(1, $itemsPerPage);
        $page = max(1, $page);

        $paginator = new Paginator($qb);
        $totalItems = count($paginator);
        $pagesCount = (int) ceil($totalItems / $itemsPerPage);

        $paginator->getQuery()
            ->setFirstResult($itemsPerPage * ($page - 1))
            ->setMaxResults($itemsPerPage);

        return [
            'items' => $paginator->getQuery()->getResult(),
            'totalPageCount' => $pagesCount,
            'totalItems' => $totalItems,
        ];
    }
}
