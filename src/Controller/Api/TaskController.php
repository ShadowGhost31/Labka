<?php

namespace App\Controller\Api;

use App\Service\EntityFactory;
use App\Service\RequestValidator;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Repository\TaskStatusRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/tasks')]
final class TaskController extends BaseApiController
{
    #[Route('', methods: ['GET'])]
    public function index(TaskRepository $repo): Response
    {
        return $this->jsonOk($repo->findAll());
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, TaskRepository $repo): Response
    {
        $entity = $repo->find($id);
        return $entity ? $this->jsonOk($entity) : $this->jsonError('Not found', 404);
    }

    #[Route('', methods: ['POST'])]
    public function create(
        Request $request,
        EntityManagerInterface $em,
        ProjectRepository $projects,
        TaskStatusRepository $statuses,
        UserRepository $users,
        RequestValidator $v,
        EntityFactory $factory
    ): Response {
        $data = $this->getJson($request);

        try {
            $v->requireFields($data, ['title','projectId','statusId','creatorId']);
            $projectId = $v->requireInt($data['projectId'] ?? null, 'projectId');
            $statusId  = $v->requireInt($data['statusId'] ?? null, 'statusId');
            $creatorId = $v->requireInt($data['creatorId'] ?? null, 'creatorId');

            $title = $v->requireString($data['title'] ?? null, 'title', 1, 200);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }

        $project = $projects->find($projectId);
        $status  = $statuses->find($statusId);
        $creator = $users->find($creatorId);

        if (!$project) return $this->jsonError('Project not found', 404);
        if (!$status)  return $this->jsonError('Status not found', 404);
        if (!$creator) return $this->jsonError('Creator not found', 404);

        $desc = $v->optionalString($data['description'] ?? null, 5000);
        $dueAt = $v->optionalDateTimeImmutable($data['dueAt'] ?? null, 'dueAt');

        $assignee = null;
        if (array_key_exists('assigneeId', $data) && $data['assigneeId'] !== null && $data['assigneeId'] !== '') {
            $assignee = $users->find((int) $data['assigneeId']);
            if (!$assignee) return $this->jsonError('Assignee not found', 404);
        }

        $entity = $factory->createTask($title, $desc, $project, $status, $creator, $assignee, $dueAt);

        $em->persist($entity);
        $this->flush($em);

        return $this->jsonOk($entity, 201);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(
        int $id,
        Request $request,
        TaskRepository $repo,
        ProjectRepository $projects,
        TaskStatusRepository $statuses,
        UserRepository $users,
        EntityManagerInterface $em,
        RequestValidator $v
    ): Response {
        $entity = $repo->find($id);
        if (!$entity) return $this->jsonError('Not found', 404);

        $data = $this->getJson($request);

        if (isset($data['title'])) {
            try {
                $entity->setTitle($v->requireString($data['title'], 'title', 1, 200));
            } catch (\Throwable $e) {
                return $this->jsonError($e->getMessage(), 400);
            }
        }

        if (array_key_exists('description', $data)) {
            $entity->setDescription($data['description'] !== null ? (string) $data['description'] : null);
        }

        if (isset($data['projectId'])) {
            $project = $projects->find((int)$data['projectId']);
            if (!$project) return $this->jsonError('Project not found', 404);
            $entity->setProject($project);
        }

        if (isset($data['statusId'])) {
            $status = $statuses->find((int)$data['statusId']);
            if (!$status) return $this->jsonError('Status not found', 404);
            $entity->setStatus($status);
        }

        if (isset($data['creatorId'])) {
            $creator = $users->find((int)$data['creatorId']);
            if (!$creator) return $this->jsonError('Creator not found', 404);
            $entity->setCreator($creator);
        }

        if (array_key_exists('assigneeId', $data)) {
            if ($data['assigneeId'] === null || $data['assigneeId'] === '') {
                $entity->setAssignee(null);
            } else {
                $assignee = $users->find((int)$data['assigneeId']);
                if (!$assignee) return $this->jsonError('Assignee not found', 404);
                $entity->setAssignee($assignee);
            }
        }

        if (array_key_exists('dueAt', $data)) {
            if ($data['dueAt'] === null || $data['dueAt'] === '') {
                $entity->setDueAt(null);
            } else {
                try {
                    $entity->setDueAt($v->optionalDateTimeImmutable($data['dueAt'], 'dueAt'));
                } catch (\Throwable $e) {
                    return $this->jsonError($e->getMessage(), 400);
                }
            }
        }

        $this->flush($em);
        return $this->jsonOk($entity);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, TaskRepository $repo, EntityManagerInterface $em): Response
    {
        $entity = $repo->find($id);
        if (!$entity) return $this->jsonError('Not found', 404);

        $em->remove($entity);
        $this->flush($em);

        return $this->jsonOk(['status' => 'deleted']);
    }
}
