<?php

namespace App\Services\Project;

use App\Entity\Project;
use App\Entity\User;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;

final class ProjectService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestChecker
    ) {}

    public function create(string $title, ?string $description, User $owner): Project
    {
        $project = new Project();
        $project->setTitle($title);
        $project->setDescription($description);
        $project->setOwner($owner);
        $project->setCreatedAt(new \DateTimeImmutable());

        $this->requestChecker->validateRequestDataByConstraints($project);
        $this->entityManager->persist($project);
        return $project;
    }

    /** @param array<string,mixed> $data */
    public function update(Project $project, array $data, ?User $owner = null): void
    {
        if (array_key_exists('title', $data)) $project->setTitle((string)$data['title']);
        if (array_key_exists('description', $data)) $project->setDescription($data['description'] !== null ? (string)$data['description'] : null);
        if ($owner) $project->setOwner($owner);

        $this->requestChecker->validateRequestDataByConstraints($project);
    }
}
