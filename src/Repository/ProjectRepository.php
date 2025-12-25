<?php

namespace App\Repository;

use App\Entity\Project;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;
use Doctrine\ORM\Tools\Pagination\Paginator;

/**
 * @extends ServiceEntityRepository<Project>
 */
final class ProjectRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Project::class);
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

        if (isset($data['ownerId'])) {
            $qb->andWhere('e.owner = :ownerId')
                ->setParameter('ownerId', (int) $data['ownerId']);
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
