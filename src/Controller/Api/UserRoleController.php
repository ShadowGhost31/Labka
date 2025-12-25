<?php

namespace App\Controller\Api;

use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Repository\UserRoleRepository;
use App\Services\RequestCheckerService;
use App\Services\UserRole\UserRoleService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/api/user-roles')]
final class UserRoleController extends BaseApiController
{
    private const REQUIRED_FIELDS_FOR_CREATE = ['userId','roleId'];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestChecker,
        private readonly UserRoleService $service,
        private readonly UserRepository $users,
        private readonly RoleRepository $roles
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(UserRoleRepository $repo): Response
    {
        return $this->jsonOk($repo->findAll());
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, UserRoleRepository $repo): Response
    {
        $ur = $repo->find($id);
        if (!$ur) throw new NotFoundHttpException('Not found');
        return $this->jsonOk($ur);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->requestChecker->check($data, self::REQUIRED_FIELDS_FOR_CREATE);

        $user = $this->users->find((int)$data['userId']);
        $role = $this->roles->find((int)$data['roleId']);
        if (!$user) throw new NotFoundHttpException('User not found');
        if (!$role) throw new NotFoundHttpException('Role not found');

        $ur = $this->service->create($user, $role);

        $this->entityManager->flush();
        return new JsonResponse($ur, Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(int $id, Request $request, UserRoleRepository $repo): JsonResponse
    {
        $ur = $repo->find($id);
        if (!$ur) throw new NotFoundHttpException('Not found');

        $data = json_decode($request->getContent(), true) ?? [];

        $user = isset($data['userId']) ? $this->users->find((int)$data['userId']) : null;
        $role = isset($data['roleId']) ? $this->roles->find((int)$data['roleId']) : null;

        if (isset($data['userId']) && !$user) throw new NotFoundHttpException('User not found');
        if (isset($data['roleId']) && !$role) throw new NotFoundHttpException('Role not found');

        $this->service->update($ur, $user, $role);

        $this->entityManager->flush();
        return new JsonResponse($ur, Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, UserRoleRepository $repo): JsonResponse
    {
        $ur = $repo->find($id);
        if (!$ur) throw new NotFoundHttpException('Not found');

        $this->entityManager->remove($ur);
        $this->entityManager->flush();
        return new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
    }
}
