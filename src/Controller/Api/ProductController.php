<?php

namespace App\Controller\Api;

use App\Entity\Product;
use App\Repository\CategoryRepository;
use App\Repository\ProductRepository;
use App\Service\RequestValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/products')]
final class ProductController extends BaseApiController
{
    #[Route('', methods: ['GET'])]
    public function index(Request $request, ProductRepository $repo): Response
    {
        $requestData = $request->query->all();
        $itemsPerPage = (int) ($requestData['itemsPerPage'] ?? 10);
        $page = (int) ($requestData['page'] ?? 1);

        unset($requestData['itemsPerPage'], $requestData['page']);

        return $this->jsonOk($repo->getAllByFilter($requestData, $itemsPerPage, $page));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, ProductRepository $repo): Response
    {
        $entity = $repo->find($id);
        if (!$entity) {
            return $this->jsonError('Not found', 404);
        }
        return $this->jsonOk($entity);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, RequestValidator $v, CategoryRepository $categories): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = $this->getJson($request);
        try {
            $v->requireFields($data, ['categoryId', 'name', 'price']);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }

        $categoryId = $v->requireInt($data['categoryId'] ?? null, 'categoryId');
        $category = $categories->find($categoryId);
        if (!$category) {
            return $this->jsonError('Category not found', 404);
        }

        $entity = new Product();
        $entity->setCategory($category);
        $entity->setName((string) $data['name']);
        $entity->setPrice((string) $data['price']);

        $em->persist($entity);
        $this->flush($em);

        return $this->jsonOk($entity, 201);
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request, ProductRepository $repo, EntityManagerInterface $em, CategoryRepository $categories, RequestValidator $v): Response
    {
        $entity = $repo->find($id);
        if (!$entity) {
            return $this->jsonError('Not found', 404);
        }

        $data = $this->getJson($request);

        if (isset($data['categoryId'])) {
            $categoryId = $v->requireInt($data['categoryId'], 'categoryId');
            $category = $categories->find($categoryId);
            if (!$category) {
                return $this->jsonError('Category not found', 404);
            }
            $entity->setCategory($category);
        }
        if (isset($data['name'])) { $entity->setName((string) $data['name']); }
        if (isset($data['price'])) { $entity->setPrice((string) $data['price']); }

        $this->flush($em);
        return $this->jsonOk($entity);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, ProductRepository $repo, EntityManagerInterface $em): Response
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
