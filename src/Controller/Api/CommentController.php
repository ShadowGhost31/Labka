<?php

namespace App\Controller\Api;

use App\Entity\Comment;
use App\Repository\CommentRepository;
use App\Repository\TaskRepository;
use App\Repository\UserRepository;
use App\Service\RequestValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/comments')]
final class CommentController extends BaseApiController
{
    #[Route('', methods: ['GET'])]
    public function index(CommentRepository $repo): Response
    {
        return $this->jsonOk($repo->findAll());
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, CommentRepository $repo): Response
    {
        $entity = $repo->find($id);
        return $entity ? $this->jsonOk($entity) : $this->jsonError('Not found', 404);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, TaskRepository $tasks, UserRepository $users, RequestValidator $v): Response
    {
        $data = $this->getJson($request);

        try {
            $v->requireFields($data, ['content','taskId','authorId']);
            $taskId = $v->requireInt($data['taskId'] ?? null, 'taskId');
            $authorId = $v->requireInt($data['authorId'] ?? null, 'authorId');
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }

        $task = $tasks->find($taskId);
        $author = $users->find($authorId);
        if (!$task) return $this->jsonError('Task not found', 404);
        if (!$author) return $this->jsonError('Author not found', 404);

        $entity = new Comment();
        $entity->setContent((string)$data['content']);
        $entity->setTask($task);
        $entity->setAuthor($author);

        $em->persist($entity);
        $this->flush($em);

        return $this->jsonOk($entity, 201);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(int $id, Request $request, CommentRepository $repo, TaskRepository $tasks, UserRepository $users, EntityManagerInterface $em): Response
    {
        $entity = $repo->find($id);
        if (!$entity) return $this->jsonError('Not found', 404);

        $data = $this->getJson($request);

        if (isset($data['content'])) $entity->setContent((string)$data['content']);
        if (isset($data['taskId'])) {
            $task = $tasks->find((int)$data['taskId']);
            if (!$task) return $this->jsonError('Task not found', 404);
            $entity->setTask($task);
        }
        if (isset($data['authorId'])) {
            $author = $users->find((int)$data['authorId']);
            if (!$author) return $this->jsonError('Author not found', 404);
            $entity->setAuthor($author);
        }

        $this->flush($em);
        return $this->jsonOk($entity);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, CommentRepository $repo, EntityManagerInterface $em): Response
    {
        $entity = $repo->find($id);
        if (!$entity) return $this->jsonError('Not found', 404);

        $em->remove($entity);
        $this->flush($em);
        return $this->jsonOk(['status' => 'deleted']);
    }
}
