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
#[ORM\Table(name: 'attachments')]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['attachment:read']]),
        new Post(denormalizationContext: ['groups' => ['attachment:write']], normalizationContext: ['groups' => ['attachment:read']]),
        new Get(normalizationContext: ['groups' => ['attachment:read']]),
        new Patch(denormalizationContext: ['groups' => ['attachment:write']], normalizationContext: ['groups' => ['attachment:read']]),
        new Delete(),
    ]
)]
class Attachment
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["attachment:read"])]
    private ?int $id = null;

    #[ORM\Column(length: 255)]
    #[Groups(["attachment:read", "attachment:write"])]
    private ?string $filename = null;

    #[ORM\Column(length: 512)]
    #[Groups(["attachment:read", "attachment:write"])]
    private ?string $path = null;

    #[ORM\Column]
    #[Groups(["attachment:read", "attachment:write"])]
    private ?\DateTimeImmutable $createdAt = null;

    #[ORM\ManyToOne(inversedBy: 'attachments')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["attachment:read", "attachment:write"])]
    private ?User $uploadedBy = null;

    #[ORM\ManyToOne(inversedBy: 'attachments')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["attachment:read", "attachment:write"])]
    private ?Task $task = null;

    public function __construct()
    {
        $this->createdAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getFilename(): ?string { return $this->filename; }
    public function setFilename(string $filename): static { $this->filename = $filename; return $this; }

    public function getPath(): ?string { return $this->path; }
    public function setPath(string $path): static { $this->path = $path; return $this; }

    public function getCreatedAt(): ?\DateTimeImmutable { return $this->createdAt; }

    public function getUploadedBy(): ?User { return $this->uploadedBy; }
    public function setUploadedBy(User $uploadedBy): static { $this->uploadedBy = $uploadedBy; return $this; }

    public function getTask(): ?Task { return $this->task; }
    public function setTask(Task $task): static { $this->task = $task; return $this; }

    public function setCreatedAt(\DateTimeImmutable $createdAt): static
    {
        $this->createdAt = $createdAt;

        return $this;
    }
}
