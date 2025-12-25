<?php

namespace App\Controller\Api;

use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Repository\TaskStatusRepository;
use App\Repository\UserRepository;
use App\Services\RequestCheckerService;
use App\Services\Task\TaskService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/api/tasks')]
final class TaskController extends BaseApiController
{
    private const REQUIRED_FIELDS_FOR_CREATE = ['title','projectId','statusId','creatorId'];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestChecker,
        private readonly TaskService $service,
        private readonly ProjectRepository $projects,
        private readonly TaskStatusRepository $statuses,
        private readonly UserRepository $users
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(TaskRepository $repo): Response
    {
        return $this->jsonOk($repo->findAll());
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, TaskRepository $repo): Response
    {
        $task = $repo->find($id);
        if (!$task) throw new NotFoundHttpException('Not found');
        return $this->jsonOk($task);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->requestChecker->check($data, self::REQUIRED_FIELDS_FOR_CREATE);

        $project = $this->projects->find((int)$data['projectId']);
        $status = $this->statuses->find((int)$data['statusId']);
        $creator = $this->users->find((int)$data['creatorId']);

        if (!$project) throw new NotFoundHttpException('Project not found');
        if (!$status) throw new NotFoundHttpException('Status not found');
        if (!$creator) throw new NotFoundHttpException('Creator not found');

        $assignee = null;
        if (isset($data['assigneeId']) && $data['assigneeId'] !== null && $data['assigneeId'] !== '') {
            $assignee = $this->users->find((int)$data['assigneeId']);
            if (!$assignee) throw new NotFoundHttpException('Assignee not found');
        }

        $dueAt = null;
        if (isset($data['dueAt']) && is_string($data['dueAt']) && $data['dueAt'] !== '') {
            try { $dueAt = new \DateTimeImmutable($data['dueAt']); } catch (\Throwable) {}
        }

        $task = $this->service->create(
            (string)$data['title'],
            $data['description'] ?? null,
            $project,
            $status,
            $creator,
            $assignee,
            $dueAt
        );

        $this->entityManager->flush();
        return new JsonResponse($task, Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(int $id, Request $request, TaskRepository $repo): JsonResponse
    {
        $task = $repo->find($id);
        if (!$task) throw new NotFoundHttpException('Not found');

        $data = json_decode($request->getContent(), true) ?? [];

        $project = isset($data['projectId']) ? $this->projects->find((int)$data['projectId']) : null;
        $status = isset($data['statusId']) ? $this->statuses->find((int)$data['statusId']) : null;
        $creator = isset($data['creatorId']) ? $this->users->find((int)$data['creatorId']) : null;

        if (isset($data['projectId']) && !$project) throw new NotFoundHttpException('Project not found');
        if (isset($data['statusId']) && !$status) throw new NotFoundHttpException('Status not found');
        if (isset($data['creatorId']) && !$creator) throw new NotFoundHttpException('Creator not found');

        $assignee = null;
        if (array_key_exists('assigneeId', $data)) {
            if ($data['assigneeId'] !== null && $data['assigneeId'] !== '') {
                $assignee = $this->users->find((int)$data['assigneeId']);
                if (!$assignee) throw new NotFoundHttpException('Assignee not found');
            }
        }

        $this->service->update($task, $data, $project, $status, $creator, $assignee);

        $this->entityManager->flush();
        return new JsonResponse($task, Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, TaskRepository $repo): JsonResponse
    {
        $task = $repo->find($id);
        if (!$task) throw new NotFoundHttpException('Not found');

        $this->entityManager->remove($task);
        $this->entityManager->flush();
        return new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
    }
}
