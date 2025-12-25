<?php

namespace App\Controller\Api;

use App\Repository\TaskRepository;
use App\Repository\TimeEntryRepository;
use App\Repository\UserRepository;
use App\Services\RequestCheckerService;
use App\Services\TimeEntry\TimeEntryService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/api/time-entries')]
final class TimeEntryController extends BaseApiController
{
    private const REQUIRED_FIELDS_FOR_CREATE = ['minutes','workDate','taskId','userId'];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestChecker,
        private readonly TimeEntryService $service,
        private readonly TaskRepository $tasks,
        private readonly UserRepository $users
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(TimeEntryRepository $repo): Response
    {
        return $this->jsonOk($repo->findAll());
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, TimeEntryRepository $repo): Response
    {
        $t = $repo->find($id);
        if (!$t) throw new NotFoundHttpException('Not found');
        return $this->jsonOk($t);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->requestChecker->check($data, self::REQUIRED_FIELDS_FOR_CREATE);

        $task = $this->tasks->find((int)$data['taskId']);
        $user = $this->users->find((int)$data['userId']);
        if (!$task) throw new NotFoundHttpException('Task not found');
        if (!$user) throw new NotFoundHttpException('User not found');

        try {
            $workDate = new \DateTimeImmutable((string)$data['workDate']);
        } catch (\Throwable) {
            // will be formatted by listener
            throw new \Symfony\Component\HttpKernel\Exception\UnprocessableEntityHttpException(json_encode([
                'workDate' => 'workDate must be a valid date (YYYY-MM-DD)'
            ]));
        }

        $t = $this->service->create((int)$data['minutes'], $workDate, $task, $user, $data['note'] ?? null);

        $this->entityManager->flush();
        return new JsonResponse($t, Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(int $id, Request $request, TimeEntryRepository $repo): JsonResponse
    {
        $t = $repo->find($id);
        if (!$t) throw new NotFoundHttpException('Not found');

        $data = json_decode($request->getContent(), true) ?? [];

        $task = isset($data['taskId']) ? $this->tasks->find((int)$data['taskId']) : null;
        $user = isset($data['userId']) ? $this->users->find((int)$data['userId']) : null;

        if (isset($data['taskId']) && !$task) throw new NotFoundHttpException('Task not found');
        if (isset($data['userId']) && !$user) throw new NotFoundHttpException('User not found');

        $this->service->update($t, $data, $task, $user);

        $this->entityManager->flush();
        return new JsonResponse($t, Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, TimeEntryRepository $repo): JsonResponse
    {
        $t = $repo->find($id);
        if (!$t) throw new NotFoundHttpException('Not found');

        $this->entityManager->remove($t);
        $this->entityManager->flush();
        return new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
    }
}
