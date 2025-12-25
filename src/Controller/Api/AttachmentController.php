<?php

namespace App\Controller\Api;

use App\Entity\Attachment;
use App\Repository\AttachmentRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Service\RequestValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/attachments')]
final class AttachmentController extends BaseApiController
{
    #[Route('', methods: ['GET'])]
    public function index(AttachmentRepository $repo): Response
    {
        return $this->jsonOk($repo->findAll());
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, AttachmentRepository $repo): Response
    {
        $entity = $repo->find($id);
        return $entity ? $this->jsonOk($entity) : $this->jsonError('Not found', 404);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, TaskRepository $tasks, UserRepository $users, RequestValidator $v): Response
    {
        $data = $this->getJson($request);

        try {
            $v->requireFields($data, ['filename','path','taskId','uploadedById']);
            $taskId = $v->requireInt($data['taskId'] ?? null, 'taskId');
            $uploadedById = $v->requireInt($data['uploadedById'] ?? null, 'uploadedById');
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }

        $task = $tasks->find($taskId);
        $user = $users->find($uploadedById);
        if (!$task) return $this->jsonError('Task not found', 404);
        if (!$user) return $this->jsonError('User not found', 404);

        $entity = new Attachment();
        $entity->setFilename((string)$data['filename']);
        $entity->setPath((string)$data['path']);
        $entity->setTask($task);
        $entity->setUploadedBy($user);

        $em->persist($entity);
        $this->flush($em);

        return $this->jsonOk($entity, 201);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(int $id, Request $request, AttachmentRepository $repo, TaskRepository $tasks, UserRepository $users, EntityManagerInterface $em): Response
    {
        $entity = $repo->find($id);
        if (!$entity) return $this->jsonError('Not found', 404);

        $data = $this->getJson($request);

        if (isset($data['filename'])) $entity->setFilename((string)$data['filename']);
        if (isset($data['path'])) $entity->setPath((string)$data['path']);

        if (isset($data['taskId'])) {
            $task = $tasks->find((int)$data['taskId']);
            if (!$task) return $this->jsonError('Task not found', 404);
            $entity->setTask($task);
        }
        if (isset($data['uploadedById'])) {
            $user = $users->find((int)$data['uploadedById']);
            if (!$user) return $this->jsonError('User not found', 404);
            $entity->setUploadedBy($user);
        }

        $this->flush($em);
        return $this->jsonOk($entity);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, AttachmentRepository $repo, EntityManagerInterface $em): Response
    {
        $entity = $repo->find($id);
        if (!$entity) return $this->jsonError('Not found', 404);

        $em->remove($entity);
        $this->flush($em);
        return $this->jsonOk(['status' => 'deleted']);
    }
}
