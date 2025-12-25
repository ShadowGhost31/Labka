<?php

namespace App\Controller\Api;

use App\Entity\TaskLabel;
use App\Repository\LabelRepository;
use App\Repository\TaskLabelRepository;
use App\Repository\TaskRepository;
use App\Service\RequestValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/task-labels')]
final class TaskLabelController extends BaseApiController
{
    #[Route('', methods: ['GET'])]
    public function index(Request $request, TaskLabelRepository $repo): Response
{
    $requestData = $request->query->all();
    $itemsPerPage = (int) ($requestData['itemsPerPage'] ?? 10);
    $page = (int) ($requestData['page'] ?? 1);

    unset($requestData['itemsPerPage'], $requestData['page']);

    return $this->jsonOk($repo->getAllByFilter($requestData, $itemsPerPage, $page));
}

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, TaskLabelRepository $repo): Response
    {
        $entity = $repo->find($id);
        return $entity ? $this->jsonOk($entity) : $this->jsonError('Not found', 404);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, TaskRepository $tasks, LabelRepository $labels, RequestValidator $v): Response
    {
        $data = $this->getJson($request);

        try {
            $v->requireFields($data, ['taskId','labelId']);
            $taskId = $v->requireInt($data['taskId'] ?? null, 'taskId');
            $labelId = $v->requireInt($data['labelId'] ?? null, 'labelId');
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }

        $task = $tasks->find($taskId);
        $label = $labels->find($labelId);
        if (!$task) return $this->jsonError('Task not found', 404);
        if (!$label) return $this->jsonError('Label not found', 404);

        $entity = new TaskLabel();
        $entity->setTask($task);
        $entity->setLabel($label);

        $em->persist($entity);
        $this->flush($em);

        return $this->jsonOk($entity, 201);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(int $id, Request $request, TaskLabelRepository $repo, TaskRepository $tasks, LabelRepository $labels, EntityManagerInterface $em): Response
    {
        $entity = $repo->find($id);
        if (!$entity) return $this->jsonError('Not found', 404);

        $data = $this->getJson($request);

        if (isset($data['taskId'])) {
            $task = $tasks->find((int)$data['taskId']);
            if (!$task) return $this->jsonError('Task not found', 404);
            $entity->setTask($task);
        }
        if (isset($data['labelId'])) {
            $label = $labels->find((int)$data['labelId']);
            if (!$label) return $this->jsonError('Label not found', 404);
            $entity->setLabel($label);
        }

        $this->flush($em);
        return $this->jsonOk($entity);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, TaskLabelRepository $repo, EntityManagerInterface $em): Response
    {
        $entity = $repo->find($id);
        if (!$entity) return $this->jsonError('Not found', 404);

        $em->remove($entity);
        $this->flush($em);
        return $this->jsonOk(['status' => 'deleted']);
    }
}
