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
#[ORM\Table(name: 'project_members', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_project_user', columns: ['project_id', 'user_id'])
])]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['project_member:read']]),
        new Post(denormalizationContext: ['groups' => ['project_member:write']], normalizationContext: ['groups' => ['project_member:read']]),
        new Get(normalizationContext: ['groups' => ['project_member:read']]),
        new Patch(denormalizationContext: ['groups' => ['project_member:write']], normalizationContext: ['groups' => ['project_member:read']]),
        new Delete(),
    ]
)]
class ProjectMember
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["project_member:read"])]
    private ?int $id = null;

    #[ORM\Column]
    #[Groups(["project_member:read", "project_member:write"])]
    private ?\DateTimeImmutable $joinedAt = null;

    #[ORM\ManyToOne(inversedBy: 'members')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["project_member:read", "project_member:write"])]
    private ?Project $project = null;

    #[ORM\ManyToOne(inversedBy: 'projectMemberships')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["project_member:read", "project_member:write"])]
    private ?User $user = null;

    #[ORM\ManyToOne]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["project_member:read", "project_member:write"])]
    private ?Role $role = null;

    public function __construct()
    {
        $this->joinedAt = new \DateTimeImmutable();
    }

    public function getId(): ?int { return $this->id; }

    public function getJoinedAt(): ?\DateTimeImmutable { return $this->joinedAt; }

    public function getProject(): ?Project { return $this->project; }
    public function setProject(Project $project): static { $this->project = $project; return $this; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(User $user): static { $this->user = $user; return $this; }

    public function getRole(): ?Role { return $this->role; }
    public function setRole(Role $role): static { $this->role = $role; return $this; }

    public function setJoinedAt(\DateTimeImmutable $joinedAt): static
    {
        $this->joinedAt = $joinedAt;

        return $this;
    }
}
