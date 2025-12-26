<?php

namespace App\Controller\Api;

use App\Entity\Category;
use App\Repository\CategoryRepository;
use App\Service\RequestValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/categories')]
final class CategoryController extends BaseApiController
{
    #[Route('', methods: ['GET'])]
    public function index(Request $request, CategoryRepository $repo): Response
    {
        $requestData = $request->query->all();
        $itemsPerPage = (int) ($requestData['itemsPerPage'] ?? 10);
        $page = (int) ($requestData['page'] ?? 1);

        unset($requestData['itemsPerPage'], $requestData['page']);

        return $this->jsonOk($repo->getAllByFilter($requestData, $itemsPerPage, $page));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, CategoryRepository $repo): Response
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
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = $this->getJson($request);
        try {
            $v->requireFields($data, ['name']);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }

        $entity = new Category();
        $entity->setName((string) $data['name']);
        $em->persist($entity);
        $this->flush($em);

        return $this->jsonOk($entity, 201);
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request, CategoryRepository $repo, EntityManagerInterface $em): Response
    {
        $entity = $repo->find($id);
        if (!$entity) {
            return $this->jsonError('Not found', 404);
        }

        $data = $this->getJson($request);
        if (isset($data['name'])) {
            $entity->setName((string) $data['name']);
        }

        $this->flush($em);
        return $this->jsonOk($entity);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, CategoryRepository $repo, EntityManagerInterface $em): Response
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
