<?php

namespace App\Controller\Api;

use App\Entity\Project;
use App\Repository\ProjectRepository;
use App\Repository\UserRepository;
use App\Service\RequestValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/projects')]
final class ProjectController extends BaseApiController
{
    #[Route('', methods: ['GET'])]
    public function index(Request $request, ProjectRepository $repo): Response
{
    $requestData = $request->query->all();
    $itemsPerPage = (int) ($requestData['itemsPerPage'] ?? 10);
    $page = (int) ($requestData['page'] ?? 1);

    unset($requestData['itemsPerPage'], $requestData['page']);

    return $this->jsonOk($repo->getAllByFilter($requestData, $itemsPerPage, $page));
}

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, ProjectRepository $repo): Response
    {
        $entity = $repo->find($id);
        return $entity ? $this->jsonOk($entity) : $this->jsonError('Not found', 404);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, UserRepository $users, RequestValidator $v): Response
    {
        $data = $this->getJson($request);
        try {
            $v->requireFields($data, ['title','ownerId']);
            $ownerId = $v->requireInt($data['ownerId'] ?? null, 'ownerId');
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }

        $owner = $users->find($ownerId);
        if (!$owner) {
            return $this->jsonError('Owner not found', 404);
        }

        $entity = new Project();
        $entity->setTitle((string)$data['title']);
        $entity->setDescription(isset($data['description']) ? (string)$data['description'] : null);
        $entity->setOwner($owner);

        $em->persist($entity);
        $this->flush($em);

        return $this->jsonOk($entity, 201);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(int $id, Request $request, ProjectRepository $repo, UserRepository $users, EntityManagerInterface $em): Response
    {
        $entity = $repo->find($id);
        if (!$entity) return $this->jsonError('Not found', 404);

        $data = $this->getJson($request);

        if (isset($data['title'])) $entity->setTitle((string)$data['title']);
        if (array_key_exists('description', $data)) $entity->setDescription($data['description'] !== null ? (string)$data['description'] : null);

        if (isset($data['ownerId'])) {
            $owner = $users->find((int)$data['ownerId']);
            if (!$owner) return $this->jsonError('Owner not found', 404);
            $entity->setOwner($owner);
        }

        $this->flush($em);
        return $this->jsonOk($entity);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, ProjectRepository $repo, EntityManagerInterface $em): Response
    {
        $entity = $repo->find($id);
        if (!$entity) return $this->jsonError('Not found', 404);

        $em->remove($entity);
        $this->flush($em);

        return $this->jsonOk(['status' => 'deleted']);
    }
}
