<?php

namespace App\Repository;

use App\Entity\ProjectMember;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<ProjectMember>
 */
final class ProjectMemberRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, ProjectMember::class);
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

        if (isset($data['projectId'])) {
            $qb->andWhere('e.project = :projectId')
                ->setParameter('projectId', (int) $data['projectId']);
        }

        if (isset($data['userId'])) {
            $qb->andWhere('e.user = :userId')
                ->setParameter('userId', (int) $data['userId']);
        }

        if (isset($data['roleId'])) {
            $qb->andWhere('e.role = :roleId')
                ->setParameter('roleId', (int) $data['roleId']);
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
