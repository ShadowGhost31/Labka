<?php

namespace App\Repository;

use App\Entity\TaskLabel;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<TaskLabel>
 */
final class TaskLabelRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TaskLabel::class);
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

        if (isset($data['taskId'])) {
            $qb->andWhere('e.task = :taskId')
                ->setParameter('taskId', (int) $data['taskId']);
        }

        if (isset($data['labelId'])) {
            $qb->andWhere('e.label = :labelId')
                ->setParameter('labelId', (int) $data['labelId']);
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
