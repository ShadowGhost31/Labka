<?php

namespace App\Entity;

use Doctrine\ORM\Mapping as ORM;
use ApiPlatform\Metadata\ApiResource;
use ApiPlatform\Metadata\Get;
use ApiPlatform\Metadata\GetCollection;
use ApiPlatform\Metadata\Post;
use ApiPlatform\Metadata\Patch;
use ApiPlatform\Metadata\Delete;
use ApiPlatform\Metadata\ApiProperty;
use Symfony\Component\Serializer\Annotation\Groups;

#[ORM\Entity]
#[ORM\Table(name: 'task_statuses')]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['task_status:read']]),
        new Post(denormalizationContext: ['groups' => ['task_status:write']], normalizationContext: ['groups' => ['task_status:read']]),
        new Get(normalizationContext: ['groups' => ['task_status:read']]),
        new Patch(denormalizationContext: ['groups' => ['task_status:write']], normalizationContext: ['groups' => ['task_status:read']]),
        new Delete(),
    ]
)]
class TaskStatus
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["task_status:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Groups(["task_status:read", "task_status:write"])]
    private ?string $name = null;

    #[ORM\Column]
    #[Groups(["task_status:read", "task_status:write"])]
    private int $sortOrder = 0;

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getSortOrder(): int { return $this->sortOrder; }
    public function setSortOrder(int $sortOrder): static { $this->sortOrder = $sortOrder; return $this; }
}
