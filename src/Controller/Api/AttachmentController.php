<?php

namespace App\Controller\Api;

use App\Repository\AttachmentRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Services\Attachment\AttachmentService;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/api/attachments')]
final class AttachmentController extends BaseApiController
{
    private const REQUIRED_FIELDS_FOR_CREATE = ['filename','path','taskId','uploadedById'];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestChecker,
        private readonly AttachmentService $service,
        private readonly TaskRepository $tasks,
        private readonly UserRepository $users
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(AttachmentRepository $repo): Response
    {
        return $this->jsonOk($repo->findAll());
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, AttachmentRepository $repo): Response
    {
        $a = $repo->find($id);
        if (!$a) throw new NotFoundHttpException('Not found');
        return $this->jsonOk($a);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->requestChecker->check($data, self::REQUIRED_FIELDS_FOR_CREATE);

        $task = $this->tasks->find((int)$data['taskId']);
        $user = $this->users->find((int)$data['uploadedById']);
        if (!$task) throw new NotFoundHttpException('Task not found');
        if (!$user) throw new NotFoundHttpException('User not found');

        $a = $this->service->create((string)$data['filename'], (string)$data['path'], $task, $user);

        $this->entityManager->flush();
        return new JsonResponse($a, Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(int $id, Request $request, AttachmentRepository $repo): JsonResponse
    {
        $a = $repo->find($id);
        if (!$a) throw new NotFoundHttpException('Not found');

        $data = json_decode($request->getContent(), true) ?? [];

        $task = isset($data['taskId']) ? $this->tasks->find((int)$data['taskId']) : null;
        $user = isset($data['uploadedById']) ? $this->users->find((int)$data['uploadedById']) : null;

        if (isset($data['taskId']) && !$task) throw new NotFoundHttpException('Task not found');
        if (isset($data['uploadedById']) && !$user) throw new NotFoundHttpException('User not found');

        $this->service->update($a, $data, $task, $user);

        $this->entityManager->flush();
        return new JsonResponse($a, Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, AttachmentRepository $repo): JsonResponse
    {
        $a = $repo->find($id);
        if (!$a) throw new NotFoundHttpException('Not found');

        $this->entityManager->remove($a);
        $this->entityManager->flush();
        return new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
    }
}
