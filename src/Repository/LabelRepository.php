<?php

namespace App\Repository;

use App\Entity\Label;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Label>
 */
final class LabelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Label::class);
    }

    /**
     * @param array $data
     * @param int $itemsPerPage
     * @param int $page
     * @return array{items: mixed, totalPageCount: int, totalItems: int}
     */
    public function getAllByFilter(array $data, int $itemsPerPage, int $page): array
    {
        $qb = $this->createQueryBuilder('e');

        if (isset($data['name'])) {
            $qb->andWhere('e.name LIKE :name')
                ->setParameter('name', '%' . $data['name'] . '%');
        }

        if (isset($data['color'])) {
            $qb->andWhere('e.color LIKE :color')
                ->setParameter('color', '%' . $data['color'] . '%');
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
