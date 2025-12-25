<?php

namespace App\Controller\Api;

use App\Repository\ProjectMemberRepository;
use App\Repository\ProjectRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Services\ProjectMember\ProjectMemberService;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/api/project-members')]
final class ProjectMemberController extends BaseApiController
{
    private const REQUIRED_FIELDS_FOR_CREATE = ['projectId','userId','roleId'];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestChecker,
        private readonly ProjectMemberService $service,
        private readonly ProjectRepository $projects,
        private readonly UserRepository $users,
        private readonly RoleRepository $roles
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(ProjectMemberRepository $repo): Response
    {
        return $this->jsonOk($repo->findAll());
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, ProjectMemberRepository $repo): Response
    {
        $pm = $repo->find($id);
        if (!$pm) throw new NotFoundHttpException('Not found');
        return $this->jsonOk($pm);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->requestChecker->check($data, self::REQUIRED_FIELDS_FOR_CREATE);

        $project = $this->projects->find((int)$data['projectId']);
        $user = $this->users->find((int)$data['userId']);
        $role = $this->roles->find((int)$data['roleId']);

        if (!$project) throw new NotFoundHttpException('Project not found');
        if (!$user) throw new NotFoundHttpException('User not found');
        if (!$role) throw new NotFoundHttpException('Role not found');

        $pm = $this->service->create($project, $user, $role);

        $this->entityManager->flush();
        return new JsonResponse($pm, Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(int $id, Request $request, ProjectMemberRepository $repo): JsonResponse
    {
        $pm = $repo->find($id);
        if (!$pm) throw new NotFoundHttpException('Not found');

        $data = json_decode($request->getContent(), true) ?? [];

        $project = isset($data['projectId']) ? $this->projects->find((int)$data['projectId']) : null;
        $user = isset($data['userId']) ? $this->users->find((int)$data['userId']) : null;
        $role = isset($data['roleId']) ? $this->roles->find((int)$data['roleId']) : null;

        if (isset($data['projectId']) && !$project) throw new NotFoundHttpException('Project not found');
        if (isset($data['userId']) && !$user) throw new NotFoundHttpException('User not found');
        if (isset($data['roleId']) && !$role) throw new NotFoundHttpException('Role not found');

        $this->service->update($pm, $project, $user, $role);

        $this->entityManager->flush();
        return new JsonResponse($pm, Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, ProjectMemberRepository $repo): JsonResponse
    {
        $pm = $repo->find($id);
        if (!$pm) throw new NotFoundHttpException('Not found');

        $this->entityManager->remove($pm);
        $this->entityManager->flush();
        return new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
    }
}
