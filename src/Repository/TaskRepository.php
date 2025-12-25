<?php

namespace App\Repository;

use App\Entity\Task;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Task>
 */
final class TaskRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Task::class);
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

        if (isset($data['title'])) {
            $qb->andWhere('e.title LIKE :title')
                ->setParameter('title', '%' . $data['title'] . '%');
        }

        if (isset($data['projectId'])) {
            $qb->andWhere('e.project = :projectId')
                ->setParameter('projectId', (int) $data['projectId']);
        }

        if (isset($data['statusId'])) {
            $qb->andWhere('e.status = :statusId')
                ->setParameter('statusId', (int) $data['statusId']);
        }

        if (isset($data['createdById'])) {
            $qb->andWhere('e.createdBy = :createdById')
                ->setParameter('createdById', (int) $data['createdById']);
        }

        if (isset($data['assignedToId'])) {
            $qb->andWhere('e.assignedTo = :assignedToId')
                ->setParameter('assignedToId', (int) $data['assignedToId']);
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
