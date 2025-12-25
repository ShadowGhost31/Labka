<?php

namespace App\Services\Task;

use App\Entity\Project;
use App\Entity\Task;
use App\Entity\TaskStatus;
use App\Entity\User;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;

final class TaskService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestChecker
    ) {}

    public function create(
        string $title,
        ?string $description,
        Project $project,
        TaskStatus $status,
        User $creator,
        ?User $assignee = null,
        ?\DateTimeImmutable $dueAt = null
    ): Task {
        $task = new Task();
        $task->setTitle($title);
        $task->setDescription($description);
        $task->setProject($project);
        $task->setStatus($status);
        $task->setCreator($creator);
        $task->setAssignee($assignee);
        $task->setDueAt($dueAt);
        $task->setCreatedAt(new \DateTimeImmutable());

        $this->requestChecker->validateRequestDataByConstraints($task);
        $this->entityManager->persist($task);
        return $task;
    }

    /** @param array<string,mixed> $data */
    public function update(Task $task, array $data, ?Project $project = null, ?TaskStatus $status = null, ?User $creator = null, ?User $assignee = null): void
    {
        if (array_key_exists('title', $data)) $task->setTitle((string)$data['title']);
        if (array_key_exists('description', $data)) $task->setDescription($data['description'] !== null ? (string)$data['description'] : null);

        if ($project) $task->setProject($project);
        if ($status) $task->setStatus($status);
        if ($creator) $task->setCreator($creator);

        if (array_key_exists('assigneeId', $data)) { // controller can pass null assignee
            $task->setAssignee($assignee);
        }

        if (array_key_exists('dueAt', $data)) {
            if ($data['dueAt'] === null || $data['dueAt'] === '') $task->setDueAt(null);
            elseif (is_string($data['dueAt'])) {
                try { $task->setDueAt(new \DateTimeImmutable($data['dueAt'])); } catch (\Throwable) {}
            }
        }

        $this->requestChecker->validateRequestDataByConstraints($task);
    }
}
