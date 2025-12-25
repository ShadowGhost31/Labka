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
#[ORM\Table(name: 'labels')]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['label:read']]),
        new Post(denormalizationContext: ['groups' => ['label:write']], normalizationContext: ['groups' => ['label:read']]),
        new Get(normalizationContext: ['groups' => ['label:read']]),
        new Patch(denormalizationContext: ['groups' => ['label:write']], normalizationContext: ['groups' => ['label:read']]),
        new Delete(),
    ]
)]
class Label
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["label:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 50, unique: true)]
    #[Groups(["label:read", "label:write"])]
    private ?string $name = null;

    #[ORM\Column(length: 16, nullable: true)]
    #[Groups(["label:read", "label:write"])]
    private ?string $color = null;

    public function getId(): ?int { return $this->id; }

    public function getName(): ?string { return $this->name; }
    public function setName(string $name): static { $this->name = $name; return $this; }

    public function getColor(): ?string { return $this->color; }
    public function setColor(?string $color): static { $this->color = $color; return $this; }
}
