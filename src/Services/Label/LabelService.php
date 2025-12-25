\<?php

namespace App\Services\Label;

use App\Entity\Label;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;

final class LabelService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestChecker
    ) {}

    public function create(string $name, ?string $color = null) : Label
    {
        $obj = new Label();
        $obj->setName($name);
        $obj->setColor($color);

        $this->requestChecker->validateRequestDataByConstraints($obj);
        $this->entityManager->persist($obj);
        return $obj;
    }

    /**
     * @param array<string, mixed> $data
     */
    public function update(Label $obj, array $data): void
    {
        if (array_key_exists('name', $data)) { $obj->setName((string)$data['name']); }
        if (array_key_exists('color', $data)) { $obj->setColor($data['color'] !== null ? (string)$data['color'] : null); }

        $this->requestChecker->validateRequestDataByConstraints($obj);
    }
}
