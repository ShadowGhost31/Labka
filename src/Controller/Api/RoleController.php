<?php

namespace App\Controller\Api;

use App\Entity\Role;
use App\Repository\RoleRepository;
use App\Service\RequestValidator;
use App\Service\EntityFactory;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/roles')]
final class RoleController extends BaseApiController
{
    #[Route('', methods: ['GET'])]
    public function index(RoleRepository $repo): Response
    {
        return $this->jsonOk($repo->findAll());
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, RoleRepository $repo): Response
    {
        $entity = $repo->find($id);
        if (!$entity) {
            return $this->jsonError('Not found', 404);
        }
        return $this->jsonOk($entity);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, RequestValidator $v, EntityFactory $factory): Response
    {
        $data = $this->getJson($request);
        try {
            $v->requireFields($data, ["name"]);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }

        $name = $v->requireString($data['name'] ?? null, 'name', 1, 50);

        $entity = $factory->createRole($name);
$em->persist($entity);
        $this->flush($em);

        return $this->jsonOk($entity, 201);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(int $id, Request $request, RoleRepository $repo, EntityManagerInterface $em): Response
    {
        $entity = $repo->find($id);
        if (!$entity) {
            return $this->jsonError('Not found', 404);
        }

        $data = $this->getJson($request);
        if (isset($data['name'])) { $entity->setName((string)$data['name']); }
        $this->flush($em);
        return $this->jsonOk($entity);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, RoleRepository $repo, EntityManagerInterface $em): Response
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
