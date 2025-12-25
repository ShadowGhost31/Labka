<?php

namespace App\Controller\Api;

use App\Entity\Task;
use App\Repository\ProjectRepository;
use App\Repository\TaskRepository;
use App\Repository\TaskStatusRepository;
use App\Repository\UserRepository;
use App\Service\RequestValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/tasks')]
final class TaskController extends BaseApiController
{
    #[Route('', methods: ['GET'])]
    public function index(Request $request, TaskRepository $repo): Response
{
    $requestData = $request->query->all();
    $itemsPerPage = (int) ($requestData['itemsPerPage'] ?? 10);
    $page = (int) ($requestData['page'] ?? 1);

    unset($requestData['itemsPerPage'], $requestData['page']);

    return $this->jsonOk($repo->getAllByFilter($requestData, $itemsPerPage, $page));
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
        RequestValidator $v
    ): Response {
        $data = $this->getJson($request);

        try {
            $v->requireFields($data, ['title','projectId','statusId','creatorId']);
            $projectId = $v->requireInt($data['projectId'] ?? null, 'projectId');
            $statusId  = $v->requireInt($data['statusId'] ?? null, 'statusId');
            $creatorId = $v->requireInt($data['creatorId'] ?? null, 'creatorId');
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }

        $project = $projects->find($projectId);
        $status = $statuses->find($statusId);
        $creator = $users->find($creatorId);

        if (!$project) return $this->jsonError('Project not found', 404);
        if (!$status) return $this->jsonError('Status not found', 404);
        if (!$creator) return $this->jsonError('Creator not found', 404);

        $entity = new Task();
        $entity->setTitle((string)$data['title']);
        $entity->setDescription(isset($data['description']) ? (string)$data['description'] : null);
        $entity->setProject($project);
        $entity->setStatus($status);
        $entity->setCreator($creator);

        if (isset($data['assigneeId']) && $data['assigneeId'] !== null && $data['assigneeId'] !== '') {
            $assignee = $users->find((int)$data['assigneeId']);
            if (!$assignee) return $this->jsonError('Assignee not found', 404);
            $entity->setAssignee($assignee);
        }

        if (isset($data['dueAt']) && is_string($data['dueAt']) && $data['dueAt'] !== '') {
            try { $entity->setDueAt(new \DateTimeImmutable($data['dueAt'])); } catch (\Throwable) {}
        }

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
        EntityManagerInterface $em
    ): Response {
        $entity = $repo->find($id);
        if (!$entity) return $this->jsonError('Not found', 404);

        $data = $this->getJson($request);

        if (isset($data['title'])) $entity->setTitle((string)$data['title']);
        if (array_key_exists('description', $data)) $entity->setDescription($data['description'] !== null ? (string)$data['description'] : null);

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
            } elseif (is_string($data['dueAt'])) {
                try { $entity->setDueAt(new \DateTimeImmutable($data['dueAt'])); } catch (\Throwable) {}
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
