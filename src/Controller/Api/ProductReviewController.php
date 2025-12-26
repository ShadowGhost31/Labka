<?php

namespace App\Controller\Api;

use App\Entity\ProductReview;
use App\Repository\ProductRepository;
use App\Repository\ProductReviewRepository;
use App\Repository\UserRepository;
use App\Service\RequestValidator;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/api/product-reviews')]
final class ProductReviewController extends BaseApiController
{
    #[Route('', methods: ['GET'])]
    public function index(Request $request, ProductReviewRepository $repo): Response
    {
        $requestData = $request->query->all();
        $itemsPerPage = (int) ($requestData['itemsPerPage'] ?? 10);
        $page = (int) ($requestData['page'] ?? 1);

        unset($requestData['itemsPerPage'], $requestData['page']);

        return $this->jsonOk($repo->getAllByFilter($requestData, $itemsPerPage, $page));
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, ProductReviewRepository $repo): Response
    {
        $entity = $repo->find($id);
        if (!$entity) {
            return $this->jsonError('Not found', 404);
        }
        return $this->jsonOk($entity);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request, EntityManagerInterface $em, RequestValidator $v, ProductRepository $products, UserRepository $users): Response
    {
        $this->denyAccessUnlessGranted('ROLE_ADMIN');

        $data = $this->getJson($request);
        try {
            $v->requireFields($data, ['productId', 'rating']);
        } catch (\Throwable $e) {
            return $this->jsonError($e->getMessage(), 400);
        }

        $productId = $v->requireInt($data['productId'] ?? null, 'productId');
        $product = $products->find($productId);
        if (!$product) {
            return $this->jsonError('Product not found', 404);
        }

        $rating = (int) $data['rating'];
        if ($rating < 1 || $rating > 5) {
            return $this->jsonError('rating must be between 1 and 5', 400);
        }

        $author = null;
        if (isset($data['authorId'])) {
            $authorId = $v->requireInt($data['authorId'], 'authorId');
            $author = $users->find($authorId);
            if (!$author) {
                return $this->jsonError('Author not found', 404);
            }
        } else {
            $author = $this->getUser();
        }

        $entity = new ProductReview();
        $entity->setProduct($product);
        if ($author instanceof \App\Entity\User) {
            $entity->setAuthor($author);
        }
        $entity->setRating($rating);
        $entity->setComment(array_key_exists('comment', $data) ? ($data['comment'] !== null ? (string) $data['comment'] : null) : null);

        $em->persist($entity);
        $this->flush($em);

        return $this->jsonOk($entity, 201);
    }

    #[Route('/{id}', methods: ['PUT', 'PATCH'])]
    public function update(int $id, Request $request, ProductReviewRepository $repo, EntityManagerInterface $em, ProductRepository $products, UserRepository $users, RequestValidator $v): Response
    {
        $entity = $repo->find($id);
        if (!$entity) {
            return $this->jsonError('Not found', 404);
        }

        $data = $this->getJson($request);

        if (isset($data['productId'])) {
            $productId = $v->requireInt($data['productId'], 'productId');
            $product = $products->find($productId);
            if (!$product) {
                return $this->jsonError('Product not found', 404);
            }
            $entity->setProduct($product);
        }

        if (isset($data['authorId'])) {
            $authorId = $v->requireInt($data['authorId'], 'authorId');
            $author = $users->find($authorId);
            if (!$author) {
                return $this->jsonError('Author not found', 404);
            }
            $entity->setAuthor($author);
        }

        if (isset($data['rating'])) {
            $rating = (int) $data['rating'];
            if ($rating < 1 || $rating > 5) {
                return $this->jsonError('rating must be between 1 and 5', 400);
            }
            $entity->setRating($rating);
        }

        if (array_key_exists('comment', $data)) {
            $entity->setComment($data['comment'] !== null ? (string) $data['comment'] : null);
        }

        $this->flush($em);
        return $this->jsonOk($entity);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, ProductReviewRepository $repo, EntityManagerInterface $em): Response
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
