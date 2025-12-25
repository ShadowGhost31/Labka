<?php

namespace App\Services\TimeEntry;

use App\Entity\Task;
use App\Entity\TimeEntry;
use App\Entity\User;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;

final class TimeEntryService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestChecker
    ) {}

    public function create(int $minutes, \DateTimeImmutable $workDate, Task $task, User $user, ?string $note = null): TimeEntry
    {
        $t = new TimeEntry();
        $t->setMinutes($minutes);
        $t->setWorkDate($workDate);
        $t->setTask($task);
        $t->setUser($user);
        $t->setNote($note);
        $this->requestChecker->validateRequestDataByConstraints($t);
        $this->entityManager->persist($t);
        return $t;
    }

    /** @param array<string,mixed> $data */
    public function update(TimeEntry $t, array $data, ?Task $task = null, ?User $user = null): void
    {
        if (array_key_exists('minutes', $data)) $t->setMinutes((int)$data['minutes']);
        if (array_key_exists('note', $data)) $t->setNote($data['note'] !== null ? (string)$data['note'] : null);

        if ($task) $t->setTask($task);
        if ($user) $t->setUser($user);

        if (array_key_exists('workDate', $data) && is_string($data['workDate'])) {
            try { $t->setWorkDate(new \DateTimeImmutable($data['workDate'])); } catch (\Throwable) {}
        }

        $this->requestChecker->validateRequestDataByConstraints($t);
    }
}
