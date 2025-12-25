<?php

declare(strict_types=1);

namespace App\Extension;

use App\Entity\Task;
use Doctrine\ORM\QueryBuilder;

/**
 * Lab 8 Extension #2
 * Filters Tasks collection/item so that non-admin users see only tasks they created.
 */
final class TaskCreatorExtension extends AbstractCurrentUserExtension
{
    public function getResourceClass(): string
    {
        return Task::class;
    }

    protected function addWhere(QueryBuilder $queryBuilder): void
    {
        $rootAlias = $queryBuilder->getRootAliases()[self::FIRST_ALIAS_INDEX];
        $queryBuilder
            ->andWhere(sprintf('%s.creator = :current_user', $rootAlias))
            ->setParameter('current_user', $this->security->getUser());
    }
}
