<?php

namespace App\Controller\Api;

use App\Repository\UserRepository;
use App\Services\User\UserService;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

#[Route('/api/users')]
final class UserController extends BaseApiController
{
    private const REQUIRED_FIELDS_FOR_CREATE = ["email", "name"];

    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestChecker,
        private readonly UserService $service
    ) {}

    #[Route('', methods: ['GET'])]
    public function index(UserRepository $repo): Response
    {
        return $this->jsonOk($repo->findAll());
    }

    #[Route('/{id}', methods: ['GET'])]
    public function show(int $id, UserRepository $repo): Response
    {
        $obj = $repo->find($id);
        if (!$obj) throw new NotFoundHttpException('Not found');
        return $this->jsonOk($obj);
    }

    #[Route('', methods: ['POST'])]
    public function create(Request $request): JsonResponse
    {
        $data = json_decode($request->getContent(), true);
        $this->requestChecker->check($data, self::REQUIRED_FIELDS_FOR_CREATE);

        $obj = $this->service->create((string)$data['email'], (string)$data['name']);

        $this->entityManager->flush();
        return new JsonResponse($obj, Response::HTTP_CREATED);
    }

    #[Route('/{id}', methods: ['PUT','PATCH'])]
    public function update(int $id, Request $request, UserRepository $repo): JsonResponse
    {
        $obj = $repo->find($id);
        if (!$obj) throw new NotFoundHttpException('Not found');

        $data = json_decode($request->getContent(), true) ?? [];
        $this->service->update($obj, $data);

        $this->entityManager->flush();
        return new JsonResponse($obj, Response::HTTP_OK);
    }

    #[Route('/{id}', methods: ['DELETE'])]
    public function delete(int $id, UserRepository $repo): JsonResponse
    {
        $obj = $repo->find($id);
        if (!$obj) throw new NotFoundHttpException('Not found');

        $this->entityManager->remove($obj);
        $this->entityManager->flush();
        return new JsonResponse(['status' => 'deleted'], Response::HTTP_OK);
    }
}
