<?php

namespace App\Controller\Api;

use App\Repository\LabelRepository;
use App\Repository\TaskLabelRepository;
use App\Repository\TaskRepository;
use App\Services\RequestCheckerService;
use App\Services\TaskLabel\TaskLabelService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/api/task-labels')]
final class TaskLabelController extends BaseApiController
{
    private const REQUIRED_FIELDS_FOR_CREATE = ['taskId','labelId'];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestChecker,
        private readonly TaskLabelService $service,
        private readonly TaskRepository $tasks,
        private readonly LabelRepository $labels
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(TaskLabelRepository $repo): Response
    {
        return $this->jsonOk($repo->findAll());
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, TaskLabelRepository $repo): Response
    {
        $tl = $repo->find($id);
        if (!$tl) throw new NotFoundHttpException('Not found');
        return $this->jsonOk($tl);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->requestChecker->check($data, self::REQUIRED_FIELDS_FOR_CREATE);

        $task = $this->tasks->find((int)$data['taskId']);
        $label = $this->labels->find((int)$data['labelId']);
        if (!$task) throw new NotFoundHttpException('Task not found');
        if (!$label) throw new NotFoundHttpException('Label not found');

        $tl = $this->service->create($task, $label);

        $this->entityManager->flush();
        return new JsonResponse($tl, Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(int $id, Request $request, TaskLabelRepository $repo): JsonResponse
    {
        $tl = $repo->find($id);
        if (!$tl) throw new NotFoundHttpException('Not found');

        $data = json_decode($request->getContent(), true) ?? [];

        $task = isset($data['taskId']) ? $this->tasks->find((int)$data['taskId']) : null;
        $label = isset($data['labelId']) ? $this->labels->find((int)$data['labelId']) : null;

        if (isset($data['taskId']) && !$task) throw new NotFoundHttpException('Task not found');
        if (isset($data['labelId']) && !$label) throw new NotFoundHttpException('Label not found');

        $this->service->update($tl, $task, $label);

        $this->entityManager->flush();
        return new JsonResponse($tl, Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, TaskLabelRepository $repo): JsonResponse
    {
        $tl = $repo->find($id);
        if (!$tl) throw new NotFoundHttpException('Not found');

        $this->entityManager->remove($tl);
        $this->entityManager->flush();
        return new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
    }
}
