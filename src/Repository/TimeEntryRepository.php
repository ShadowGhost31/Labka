<?php

namespace App\Repository;

use App\Entity\TimeEntry;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<TimeEntry>
 */
final class TimeEntryRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, TimeEntry::class);
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

        if (isset($data['userId'])) {
            $qb->andWhere('e.user = :userId')
                ->setParameter('userId', (int) $data['userId']);
        }

        if (isset($data['taskId'])) {
            $qb->andWhere('e.task = :taskId')
                ->setParameter('taskId', (int) $data['taskId']);
        }

        if (isset($data['workDate'])) {
            try {
                $dt = new \DateTimeImmutable((string) $data['workDate']);
                $qb->andWhere('e.workDate = :workDate')
                    ->setParameter('workDate', $dt);
            } catch (\Throwable $e) {
                // ignore invalid date filter
            }
        }

        if (isset($data['minMinutes'])) {
            $qb->andWhere('e.minutes >= :minMinutes')
                ->setParameter('minMinutes', (int) $data['minMinutes']);
        }

        if (isset($data['maxMinutes'])) {
            $qb->andWhere('e.minutes <= :maxMinutes')
                ->setParameter('maxMinutes', (int) $data['maxMinutes']);
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
