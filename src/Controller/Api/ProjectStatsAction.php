<?php

namespace App\Controller\Api;

use App\Entity\Project;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpKernel\Attribute\AsController;

/**
 * Lab 8 Action #1
 * GET /api/projects/{id}/stats
 */
#[AsController]
final class ProjectStatsAction
{
    public function __invoke(Project $project): JsonResponse
    {
        return new JsonResponse([
            'projectId' => $project->getId(),
            'title' => $project->getTitle(),
            'membersCount' => $project->getMembers()->count(),
            'tasksCount' => $project->getTasks()->count(),
        ]);
    }
}
