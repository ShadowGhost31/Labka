<?php

namespace App\Controller\Api;

use App\Entity\TimeEntry;
use App\Repository\TaskRepository;
use App\Repository\TimeEntryRepository;
use App\Repository\UserRepository;
use App\Service\RequestValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/time-entries')]
final class TimeEntryController extends BaseApiController
{
    #[Route('', methods: ['GET'])]
    public function index(TimeEntryRepository $repo): Response
    {
        return $this->jsonOk($repo->findAll());
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, TimeEntryRepository $repo): Response
    {
        $entity = $repo->find($id);
        return $entity ? $this->jsonOk($entity) : $this->jsonError('Not found', 404);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, TaskRepository $tasks, UserRepository $users, RequestValidator $v): Response
    {
        $data = $this->getJson($request);

        try {
            $v->requireFields($data, ['minutes','workDate','taskId','userId']);
            $taskId = $v->requireInt($data['taskId'] ?? null, 'taskId');
            $userId = $v->requireInt($data['userId'] ?? null, 'userId');
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }

        $task = $tasks->find($taskId);
        $user = $users->find($userId);
        if (!$task) return $this->jsonError('Task not found', 404);
        if (!$user) return $this->jsonError('User not found', 404);

        $entity = new TimeEntry();
        $entity->setMinutes((int)$data['minutes']);
        $entity->setTask($task);
        $entity->setUser($user);
        $entity->setNote(isset($data['note']) ? (string)$data['note'] : null);

        if (isset($data['workDate']) && is_string($data['workDate'])) {
            try { $entity->setWorkDate(new \DateTimeImmutable($data['workDate'])); } catch (\Throwable) {}
        }

        $em->persist($entity);
        $this->flush($em);

        return $this->jsonOk($entity, 201);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(int $id, Request $request, TimeEntryRepository $repo, TaskRepository $tasks, UserRepository $users, EntityManagerInterface $em): Response
    {
        $entity = $repo->find($id);
        if (!$entity) return $this->jsonError('Not found', 404);

        $data = $this->getJson($request);

        if (isset($data['minutes'])) $entity->setMinutes((int)$data['minutes']);
        if (array_key_exists('note', $data)) $entity->setNote($data['note'] !== null ? (string)$data['note'] : null);

        if (isset($data['taskId'])) {
            $task = $tasks->find((int)$data['taskId']);
            if (!$task) return $this->jsonError('Task not found', 404);
            $entity->setTask($task);
        }
        if (isset($data['userId'])) {
            $user = $users->find((int)$data['userId']);
            if (!$user) return $this->jsonError('User not found', 404);
            $entity->setUser($user);
        }

        if (array_key_exists('workDate', $data)) {
            if ($data['workDate'] === null || $data['workDate'] === '') {
                // keep previous if null (or set to today - up to you). We'll keep previous.
            } elseif (is_string($data['workDate'])) {
                try { $entity->setWorkDate(new \DateTimeImmutable($data['workDate'])); } catch (\Throwable) {}
            }
        }

        $this->flush($em);
        return $this->jsonOk($entity);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, TimeEntryRepository $repo, EntityManagerInterface $em): Response
    {
        $entity = $repo->find($id);
        if (!$entity) return $this->jsonError('Not found', 404);

        $em->remove($entity);
        $this->flush($em);
        return $this->jsonOk(['status' => 'deleted']);
    }
}
