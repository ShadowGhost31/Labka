<?php

namespace App\Services\UserRole;

use App\Entity\Role;
use App\Entity\User;
use App\Entity\UserRole;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;

final class UserRoleService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestChecker
    ) {}

    public function create(User $user, Role $role): UserRole
    {
        $ur = new UserRole();
        $ur->setUser($user);
        $ur->setRole($role);

        $this->requestChecker->validateRequestDataByConstraints($ur);
        $this->entityManager->persist($ur);
        return $ur;
    }

    public function update(UserRole $ur, ?User $user = null, ?Role $role = null): void
    {
        if ($user) $ur->setUser($user);
        if ($role) $ur->setRole($role);

        $this->requestChecker->validateRequestDataByConstraints($ur);
    }
}
