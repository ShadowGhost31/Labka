<?php

namespace App\Controller\Api;

use App\Entity\UserRole;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Repository\UserRoleRepository;
use App\Service\RequestValidator;
use App\Service\EntityFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/user-roles')]
final class UserRoleController extends BaseApiController
{
    #[Route('', methods: ['GET'])]
    public function index(UserRoleRepository $repo): Response
    {
        return $this->jsonOk($repo->findAll());
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, UserRoleRepository $repo): Response
    {
        $entity = $repo->find($id);
        return $entity ? $this->jsonOk($entity) : $this->jsonError('Not found', 404);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, UserRepository $users, RoleRepository $roles, RequestValidator $v, EntityFactory $factory): Response
    {
        $data = $this->getJson($request);

        try {
            $v->requireFields($data, ['userId','roleId']);
            $userId = $v->requireInt($data['userId'] ?? null, 'userId');
            $roleId = $v->requireInt($data['roleId'] ?? null, 'roleId');
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }

        $user = $users->find($userId);
        $role = $roles->find($roleId);

        if (!$user) return $this->jsonError('User not found', 404);
        if (!$role) return $this->jsonError('Role not found', 404);

        $entity = $factory->createUserRole($user, $role);
$em->persist($entity);
        $this->flush($em);

        return $this->jsonOk($entity, 201);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(int $id, Request $request, UserRoleRepository $repo, UserRepository $users, RoleRepository $roles, EntityManagerInterface $em): Response
    {
        $entity = $repo->find($id);
        if (!$entity) return $this->jsonError('Not found', 404);

        $data = $this->getJson($request);

        if (isset($data['userId'])) {
            $user = $users->find((int)$data['userId']);
            if (!$user) return $this->jsonError('User not found', 404);
            $entity->setUser($user);
        }
        if (isset($data['roleId'])) {
            $role = $roles->find((int)$data['roleId']);
            if (!$role) return $this->jsonError('Role not found', 404);
            $entity->setRole($role);
        }

        $this->flush($em);
        return $this->jsonOk($entity);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, UserRoleRepository $repo, EntityManagerInterface $em): Response
    {
        $entity = $repo->find($id);
        if (!$entity) return $this->jsonError('Not found', 404);

        $em->remove($entity);
        $this->flush($em);

        return $this->jsonOk(['status' => 'deleted']);
    }
}
