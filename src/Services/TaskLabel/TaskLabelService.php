<?php

namespace App\Services\TaskLabel;

use App\Entity\Label;
use App\Entity\Task;
use App\Entity\TaskLabel;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;

final class TaskLabelService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestChecker
    ) {}

    public function create(Task $task, Label $label): TaskLabel
    {
        $tl = new TaskLabel();
        $tl->setTask($task);
        $tl->setLabel($label);

        $this->requestChecker->validateRequestDataByConstraints($tl);
        $this->entityManager->persist($tl);
        return $tl;
    }

    public function update(TaskLabel $tl, ?Task $task = null, ?Label $label = null): void
    {
        if ($task) $tl->setTask($task);
        if ($label) $tl->setLabel($label);

        $this->requestChecker->validateRequestDataByConstraints($tl);
    }
}
