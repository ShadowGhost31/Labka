<?php

namespace App\Services\ProjectMember;

use App\Entity\Project;
use App\Entity\ProjectMember;
use App\Entity\Role;
use App\Entity\User;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;

final class ProjectMemberService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestChecker
    ) {}

    public function create(Project $project, User $user, Role $role): ProjectMember
    {
        $pm = new ProjectMember();
        $pm->setProject($project);
        $pm->setUser($user);
        $pm->setRole($role);

        $this->requestChecker->validateRequestDataByConstraints($pm);
        $this->entityManager->persist($pm);
        return $pm;
    }

    /** @param array<string,mixed> $data */
    public function update(ProjectMember $pm, ?Project $project = null, ?User $user = null, ?Role $role = null): void
    {
        if ($project) $pm->setProject($project);
        if ($user) $pm->setUser($user);
        if ($role) $pm->setRole($role);

        $this->requestChecker->validateRequestDataByConstraints($pm);
    }
}
