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
#[ORM\Table(name: 'user_roles', uniqueConstraints: [
    new ORM\UniqueConstraint(name: 'uniq_user_role', columns: ['user_id', 'role_id'])
])]
#[ApiResource(
    operations: [
        new GetCollection(normalizationContext: ['groups' => ['user_role:read']]),
        new Post(denormalizationContext: ['groups' => ['user_role:write']], normalizationContext: ['groups' => ['user_role:read']]),
        new Get(normalizationContext: ['groups' => ['user_role:read']]),
        new Patch(denormalizationContext: ['groups' => ['user_role:write']], normalizationContext: ['groups' => ['user_role:read']]),
        new Delete(),
    ]
)]
class UserRole
{
    #[ORM\Id]
    #[ORM\GeneratedValue]
    #[ORM\Column]
    #[Groups(["user_role:read"])]
    private ?int $id = null;

    #[ORM\ManyToOne(inversedBy: 'userRoles')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["user_role:read", "user_role:write"])]
    private ?User $user = null;

    #[ORM\ManyToOne(inversedBy: 'userRoles')]
    #[ORM\JoinColumn(nullable: false)]
    #[ApiProperty(readableLink: false, writableLink: false)]
    #[Groups(["user_role:read", "user_role:write"])]
    private ?Role $role = null;

    public function getId(): ?int { return $this->id; }

    public function getUser(): ?User { return $this->user; }
    public function setUser(User $user): static { $this->user = $user; return $this; }

    public function getRole(): ?Role { return $this->role; }
    public function setRole(Role $role): static { $this->role = $role; return $this; }
}
