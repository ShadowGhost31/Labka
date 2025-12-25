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
#[ORM\Table(name: 'task_labels', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_task_label', columns: ['task_id', 'label_id'])
])]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['task_label:read']]),
        new Post(denormalizationContext: ['groups' => ['task_label:write']], normalizationContext: ['groups' => ['task_label:read']]),
        new Get(normalizationContext: ['groups' => ['task_label:read']]),
        new Patch(denormalizationContext: ['groups' => ['task_label:write']], normalizationContext: ['groups' => ['task_label:read']]),
        new Delete(),
    ]
)]
class TaskLabel
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["task_label:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'taskLabels')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["task_label:read", "task_label:write"])]
    private ?Task $task = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["task_label:read", "task_label:write"])]
    private ?Label $label = null;

    public function getId(): ?int { return $this->id; }

    public function getTask(): ?Task { return $this->task; }
    public function setTask(Task $task): static { $this->task = $task; return $this; }

    public function getLabel(): ?Label { return $this->label; }
    public function setLabel(Label $label): static { $this->label = $label; return $this; }
}
