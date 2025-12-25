<?php

namespace App\Controller\Api;

use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use App\Services\Project\ProjectService;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/api/projects')]
final class ProjectController extends BaseApiController
{
    private const REQUIRED_FIELDS_FOR_CREATE = ['title','ownerId'];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestChecker,
        private readonly ProjectService $service,
        private readonly UserRepository $users
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(ProjectRepository $repo): Response
    {
        return $this->jsonOk($repo->findAll());
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, ProjectRepository $repo): Response
    {
        $p = $repo->find($id);
        if (!$p) throw new NotFoundHttpException('Not found');
        return $this->jsonOk($p);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->requestChecker->check($data, self::REQUIRED_FIELDS_FOR_CREATE);

        $owner = $this->users->find((int)$data['ownerId']);
        if (!$owner) throw new NotFoundHttpException('Owner not found');

        $project = $this->service->create(
            (string)$data['title'],
            $data['description'] ?? null,
            $owner
        );

        $this->entityManager->flush();
        return new JsonResponse($project, Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(int $id, Request $request, ProjectRepository $repo): JsonResponse
    {
        $project = $repo->find($id);
        if (!$project) throw new NotFoundHttpException('Not found');

        $data = json_decode($request->getContent(), true) ?? [];

        $owner = null;
        if (isset($data['ownerId'])) {
            $owner = $this->users->find((int)$data['ownerId']);
            if (!$owner) throw new NotFoundHttpException('Owner not found');
        }

        $this->service->update($project, $data, $owner);

        $this->entityManager->flush();
        return new JsonResponse($project, Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, ProjectRepository $repo): JsonResponse
    {
        $project = $repo->find($id);
        if (!$project) throw new NotFoundHttpException('Not found');

        $this->entityManager->remove($project);
        $this->entityManager->flush();
        return new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
    }
}
