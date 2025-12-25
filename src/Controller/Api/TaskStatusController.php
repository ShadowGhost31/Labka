<?php

namespace App\Controller\Api;

use App\Entity\TaskStatus;
use App\Repository\TaskStatusRepository;
use App\Service\RequestValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/task-statuses')]
final class TaskStatusController extends BaseApiController
{
    #[Route('', methods: ['GET'])]
    public function index(TaskStatusRepository $repo): Response
    {
        return $this->jsonOk($repo->findAll());
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, TaskStatusRepository $repo): Response
    {
        $entity = $repo->find($id);
        if (!$entity) {
            return $this->jsonError('Not found', 404);
        }
        return $this->jsonOk($entity);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, RequestValidator $v): Response
    {
        $data = $this->getJson($request);
        try {
            $v->requireFields($data, ["name"]);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }

        $entity = new TaskStatus();
        $entity->setName((string)($data['name'] ?? ''));
                if (isset($data['sortOrder'])) { $entity->setSortOrder((int)$data['sortOrder']); }
        $em->persist($entity);
        $this->flush($em);

        return $this->jsonOk($entity, 201);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(int $id, Request $request, TaskStatusRepository $repo, EntityManagerInterface $em): Response
    {
        $entity = $repo->find($id);
        if (!$entity) {
            return $this->jsonError('Not found', 404);
        }

        $data = $this->getJson($request);
        if (isset($data['name'])) { $entity->setName((string)$data['name']); }
                if (isset($data['sortOrder'])) { $entity->setSortOrder((int)$data['sortOrder']); }
        $this->flush($em);
        return $this->jsonOk($entity);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, TaskStatusRepository $repo, EntityManagerInterface $em): Response
    {
        $entity = $repo->find($id);
        if (!$entity) {
            return $this->jsonError('Not found', 404);
        }
        $em->remove($entity);
        $this->flush($em);
        return $this->jsonOk(['status' => 'deleted']);
    }
}
