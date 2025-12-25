<?php

namespace App\Controller\Api;

use App\Repository\CommentRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Services\Comment\CommentService;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/api/comments')]
final class CommentController extends BaseApiController
{
    private const REQUIRED_FIELDS_FOR_CREATE = ['content','taskId','authorId'];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestChecker,
        private readonly CommentService $service,
        private readonly TaskRepository $tasks,
        private readonly UserRepository $users
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(CommentRepository $repo): Response
    {
        return $this->jsonOk($repo->findAll());
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, CommentRepository $repo): Response
    {
        $c = $repo->find($id);
        if (!$c) throw new NotFoundHttpException('Not found');
        return $this->jsonOk($c);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->requestChecker->check($data, self::REQUIRED_FIELDS_FOR_CREATE);

        $task = $this->tasks->find((int)$data['taskId']);
        $author = $this->users->find((int)$data['authorId']);
        if (!$task) throw new NotFoundHttpException('Task not found');
        if (!$author) throw new NotFoundHttpException('Author not found');

        $c = $this->service->create((string)$data['content'], $task, $author);

        $this->entityManager->flush();
        return new JsonResponse($c, Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(int $id, Request $request, CommentRepository $repo): JsonResponse
    {
        $c = $repo->find($id);
        if (!$c) throw new NotFoundHttpException('Not found');

        $data = json_decode($request->getContent(), true) ?? [];

        $task = isset($data['taskId']) ? $this->tasks->find((int)$data['taskId']) : null;
        $author = isset($data['authorId']) ? $this->users->find((int)$data['authorId']) : null;

        if (isset($data['taskId']) && !$task) throw new NotFoundHttpException('Task not found');
        if (isset($data['authorId']) && !$author) throw new NotFoundHttpException('Author not found');

        $this->service->update($c, $data, $task, $author);

        $this->entityManager->flush();
        return new JsonResponse($c, Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, CommentRepository $repo): JsonResponse
    {
        $c = $repo->find($id);
        if (!$c) throw new NotFoundHttpException('Not found');

        $this->entityManager->remove($c);
        $this->entityManager->flush();
        return new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
    }
}
