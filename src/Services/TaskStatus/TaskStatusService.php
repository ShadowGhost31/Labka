\<?php

namespace App\Services\TaskStatus;

use App\Entity\TaskStatus;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;

final class TaskStatusService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestChecker
    ) {}

    public function create(string $name, int $sortOrder = 0) : TaskStatus
    {
        $obj = new TaskStatus();
        $obj->setName($name);
        $obj->setSortOrder($sortOrder);

        $this->requestChecker->validateRequestDataByConstraints($obj);
        $this->entityManager->persist($obj);
        return $obj;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(TaskStatus $obj, array $data): void
    {
        if (array_key_exists('name', $data)) { $obj->setName((string)$data['name']); }
        if (array_key_exists('sortOrder', $data)) { $obj->setSortOrder((int)$data['sortOrder']); }

        $this->requestChecker->validateRequestDataByConstraints($obj);
    }
}
