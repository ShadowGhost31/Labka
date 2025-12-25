<?php

namespace App\Services\Label;

use App\Entity\Label;
use App\Services\RequestCheckerService;
use Doctrine\ORM\EntityManagerInterface;

final class LabelService
{
    public function __construct(
        private readonly EntityManagerInterface $entityManager,
        private readonly RequestCheckerService $requestCheckerService
    ) {}

    public function createLabel(string $name, ?string $color = null): Label
    {
        $label = (new Label())
            ->setName($name)
            ->setColor($color);

        // Валідація constraint-ами Entity
        $this->requestCheckerService->validateRequestDataByConstraints($label);

        $this->entityManager->persist($label);
        return $label;
    }

    public function updateLabel(Label $label, array $data): void
    {
        if (array_key_exists('name', $data)) {
            $label->setName((string) $data['name']);
        }
        if (array_key_exists('color', $data)) {
            $label->setColor($data['color'] !== null ? (string) $data['color'] : null);
        }

        $this->requestCheckerService->validateRequestDataByConstraints($label);
    }
}
