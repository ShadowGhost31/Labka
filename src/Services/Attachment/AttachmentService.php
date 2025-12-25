<?php

namespace App\Services\Attachment;

use App\Entity\Attachment;
use App\Entity\Task;
use App\Entity\User;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;

final class AttachmentService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestChecker
    ) {}

    public function create(string $filename, string $path, Task $task, User $uploadedBy): Attachment
    {
        $a = new Attachment();
        $a->setFilename($filename);
        $a->setPath($path);
        $a->setTask($task);
        $a->setUploadedBy($uploadedBy);
        $a->setCreatedAt(new \DateTimeImmutable());

        $this->requestChecker->validateRequestDataByConstraints($a);
        $this->entityManager->persist($a);
        return $a;
    }

    /** @param array<string,mixed> $data */
    public function update(Attachment $a, array $data, ?Task $task = null, ?User $uploadedBy = null): void
    {
        if (array_key_exists('filename', $data)) $a->setFilename((string)$data['filename']);
        if (array_key_exists('path', $data)) $a->setPath((string)$data['path']);
        if ($task) $a->setTask($task);
        if ($uploadedBy) $a->setUploadedBy($uploadedBy);

        $this->requestChecker->validateRequestDataByConstraints($a);
    }
}
