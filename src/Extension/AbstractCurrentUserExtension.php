<?php

declare(strict_types=1);

namespace App\Extension;

use ApiPlatform\Doctrine\Orm\Extension\QueryCollectionExtensionInterface;
use ApiPlatform\Doctrine\Orm\Extension\QueryItemExtensionInterface;
use ApiPlatform\Doctrine\Orm\Util\QueryNameGeneratorInterface;
use Doctrine\ORM\QueryBuilder;
use Symfony\Bundle\SecurityBundle\Security;

/**
 * Base class for Lab 8 Extensions.
 * Filters collection/item queries by current user for non-admins.
 */
abstract class AbstractCurrentUserExtension implements QueryCollectionExtensionInterface, QueryItemExtensionInterface
{
    public const FIRST_ALIAS_INDEX = 0;
    public const ADMIN_ROLES = ['ROLE_ADMIN'];

    public function __construct(protected readonly Security $security) {}

    public function applyToCollection(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        ?\ApiPlatform\Metadata\Operation $operation = null,
        array $context = []
    ): void {
        if ($this->shouldSkip($resourceClass)) {
            return;
        }
        $this->addWhere($queryBuilder);
    }

    public function applyToItem(
        QueryBuilder $queryBuilder,
        QueryNameGeneratorInterface $queryNameGenerator,
        string $resourceClass,
        array $identifiers,
        ?\ApiPlatform\Metadata\Operation $operation = null,
        array $context = []
    ): void {
        if ($this->shouldSkip($resourceClass)) {
            return;
        }
        $this->addWhere($queryBuilder);
    }

    private function shouldSkip(string $resourceClass): bool
    {
        if ($resourceClass !== $this->getResourceClass()) {
            return true;
        }

        $user = $this->security->getUser();
        if (!$user) {
            // If anonymous, do not filter here (access control should block anyway)
            return true;
        }

        $roles = method_exists($user, 'getRoles') ? $user->getRoles() : [];
        if (count(array_intersect(self::ADMIN_ROLES, $roles)) > 0) {
            // Admins see everything
            return true;
        }

        return false;
    }

    abstract public function getResourceClass(): string;

    /**
     * Add your where clause to the query.
     */
    abstract protected function addWhere(QueryBuilder $queryBuilder): void;
}
