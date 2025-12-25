\<?php

namespace App\Services\User;

use App\Entity\User;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;

final class UserService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestChecker
    ) {}

    public function create(string $email, string $name) : User
    {
        $obj = new User();
        $obj->setEmail($email);
        $obj->setName($name);
        $obj->setCreatedAt(new \DateTimeImmutable());

        $this->requestChecker->validateRequestDataByConstraints($obj);
        $this->entityManager->persist($obj);
        return $obj;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(User $obj, array $data): void
    {
        if (array_key_exists('email', $data)) { $obj->setEmail((string)$data['email']); }
        if (array_key_exists('name', $data)) { $obj->setName((string)$data['name']); }

        $this->requestChecker->validateRequestDataByConstraints($obj);
    }
}
