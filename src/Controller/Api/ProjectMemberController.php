<?php

namespace App\Controller\Api;

use App\Entity\ProjectMember;
use App\Repository\ProjectMemberRepository;
use App\Repository\ProjectRepository;
use App\Repository\RoleRepository;
use App\Repository\UserRepository;
use App\Service\RequestValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/project-members')]
final class ProjectMemberController extends BaseApiController
{
    #[Route('', methods: ['GET'])]
    public function index(ProjectMemberRepository $repo): Response
    {
        return $this->jsonOk($repo->findAll());
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, ProjectMemberRepository $repo): Response
    {
        $entity = $repo->find($id);
        return $entity ? $this->jsonOk($entity) : $this->jsonError('Not found', 404);
    }

    #[Route('', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        ProjectRepository $projects,
        UserRepository $users,
        RoleRepository $roles,
        RequestValidator $v
    ): Response {
        $data = $this->getJson($request);

        try {
            $v->requireFields($data, ['projectId','userId','roleId']);
            $projectId = $v->requireInt($data['projectId'] ?? null, 'projectId');
            $userId = $v->requireInt($data['userId'] ?? null, 'userId');
            $roleId = $v->requireInt($data['roleId'] ?? null, 'roleId');
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }

        $project = $projects->find($projectId);
        $user = $users->find($userId);
        $role = $roles->find($roleId);

        if (!$project) return $this->jsonError('Project not found', 404);
        if (!$user) return $this->jsonError('User not found', 404);
        if (!$role) return $this->jsonError('Role not found', 404);

        $entity = new ProjectMember();
        $entity->setProject($project);
        $entity->setUser($user);
        $entity->setRole($role);

        $em->persist($entity);
        $this->flush($em);

        return $this->jsonOk($entity, 201);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(
        int $id,
        Request $request,
        ProjectMemberRepository $repo,
        ProjectRepository $projects,
        UserRepository $users,
        RoleRepository $roles,
        EntityManagerInterface $em
    ): Response {
        $entity = $repo->find($id);
        if (!$entity) return $this->jsonError('Not found', 404);

        $data = $this->getJson($request);

        if (isset($data['projectId'])) {
            $project = $projects->find((int)$data['projectId']);
            if (!$project) return $this->jsonError('Project not found', 404);
            $entity->setProject($project);
        }
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
    public function delete(int $id, ProjectMemberRepository $repo, EntityManagerInterface $em): Response
    {
        $entity = $repo->find($id);
        if (!$entity) return $this->jsonError('Not found', 404);

        $em->remove($entity);
        $this->flush($em);

        return $this->jsonOk(['status' => 'deleted']);
    }
}
