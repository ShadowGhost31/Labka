<?php

namespace App\Entity;

use Doctrine\DBAL\Types\Types;
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
#[ORM\Table(name: 'comments')]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['comment:read']]),
        new Post(denormalizationContext: ['groups' => ['comment:write']], normalizationContext: ['groups' => ['comment:read']]),
        new Get(normalizationContext: ['groups' => ['comment:read']]),
        new Patch(denormalizationContext: ['groups' => ['comment:write']], normalizationContext: ['groups' => ['comment:read']]),
        new Delete(),
    ]
)]
class Comment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["comment:read"])]
    private ?int $id = null;

    #[ORM\Column(type: 'text')]
    #[Groups(["comment:read", "comment:write"])]
    private ?string $content = null;

    #[ORM\Column]
    #[Groups(["comment:read", "comment:write"])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["comment:read", "comment:write"])]
    private ?User $author = null;

    #[ORM\ManyToOne(inversedBy: 'comments')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["comment:read", "comment:write"])]
    private ?Task $task = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getContent(): ?string { return $this->content; }
    public function setContent(string $content): static { $this->content = $content; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    public function getAuthor(): ?User { return $this->author; }
    public function setAuthor(User $author): static { $this->author = $author; return $this; }

    public function getTask(): ?Task { return $this->task; }
    public function setTask(Task $task): static { $this->task = $task; return $this; }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
