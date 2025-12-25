<?php

namespace App\Services\Role;

use App\Entity\Role;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;

final class RoleService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestChecker
    ) {}

    public function create(string $name) : Role
    {
        $obj = new Role();
        $obj->setName($name);

        $this->requestChecker->validateRequestDataByConstraints($obj);
        $this->entityManager->persist($obj);
        return $obj;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(Role $obj, array $data): void
    {
        if (array_key_exists('name', $data)) { $obj->setName((string)$data['name']); }

        $this->requestChecker->validateRequestDataByConstraints($obj);
    }
}
