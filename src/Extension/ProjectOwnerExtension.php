<?php

declare(strict_types=1);

namespace App\Extension;

use App\Entity\Project;
use Doctrine\ORM\QueryBuilder;

/**
 * Lab 8 Extension #1
 * Filters Projects collection/item so that non-admin users see only their own projects.
 */
final class ProjectOwnerExtension extends AbstractCurrentUserExtension
{
    public function getResourceClass(): string
    {
        return Project::class;
    }

    protected function addWhere(QueryBuilder $queryBuilder): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[self::FIRST_ALIAS_INDEX];
        $queryBuilder
            ->andWhere(sprintf('%s.owner = :current_user', $rootAlias))
            ->setParameter('current_user', $this->security->getUser());
    }
}
